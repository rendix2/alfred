<?php

namespace Alfred\App\WebModule\Components\Word;

use Alfred\App\Model\Entity\CategoryEntity;
use Alfred\App\Model\Entity\WordEntity;
use FreezyBee\DoctrineFormMapper\DoctrineFormMapper;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nettrine\ORM\EntityManagerDecorator;

/**
 * class CategoriesCard
 *
 * @package Alfred\App\WebModule\Components\Word
 */
class CategoriesCard extends Control
{

    public function __construct
    (
        private EntityManagerDecorator $em,
        private DoctrineFormMapper     $doctrineFormMapper,
        private WordEntity             $word,
    ) {

    }

    public function render()
    {
        $sep = DIRECTORY_SEPARATOR;
        $template = $this->template;

        $template->word = $this->word;
        $template->categories = $this->getCategories();

        $template->setFile(__DIR__ . $sep . 'CategoriesCard.latte');
        $template->render();
    }

    private function getCategories() : array
    {
        $categories = $this->em
            ->getRepository(CategoryEntity::class)
            ->createQueryBuilder('_category')
            ->orderBy('_category.name')
            ->getQuery()
            ->getResult();

        foreach ($categories as $category) {
            $category->hasCategory = false;

            foreach ($this->word->categories as $wordCategory) {
                if ($category->id === $wordCategory->id) {
                    $category->hasCategory = true;
                    break;
                }
            }
        }

        return array_chunk($categories, 15);
    }

    private function getWord(int $id) : ?WordEntity
    {
        return $this->em
            ->getRepository(WordEntity::class)
            ->createQueryBuilder('_word')
            ->addSelect('_categories')
            ->leftJoin('_word.categories', '_categories')
            ->where('_word.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    private function getCategory(int $id) : ?CategoryEntity
    {
        return $this->em
            ->getRepository(CategoryEntity::class)
            ->createQueryBuilder('_category')
            ->addSelect('_word')
            ->leftJoin('_category.words', '_word')
            ->where('_category.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function handleAdd(int $wordId, int $categoryId) : void
    {
        if (!$this->presenter->isAjax()) {
            $this->presenter->redirect('Word:edit', $wordId);
        }

        /**
         * @var WordEntity $word
         */
        $word = $this->getWord($wordId);

        if (!$word) {
            $this->flashMessage('Slovo nenalezeno.', 'danger');
        }

        /**
         * @var CategoryEntity $category
         */
        $category = $this->getCategory($categoryId);

        if (!$category) {
            $this->flashMessage('Kategorie nenalezena.', 'danger');
        }

        $word->addCategory($category);

        $this->em->persist($word);
        $this->em->flush();

        $this->flashMessage('Kategorie přidána.', 'success');

        $this->redrawControl('categories');
        $this->redrawControl('flashes');
    }

    public function handleDelete(int $wordId, int $categoryId) : void
    {
        if (!$this->presenter->isAjax()) {
            $this->presenter->redirect('Word:edit', $wordId);
        }

        /**
         * @var WordEntity $word
         */
        $word = $this->getWord($wordId);

        if (!$word) {
            $this->flashMessage('Slovo nenalezeno.', 'danger');
        }

        /**
         * @var CategoryEntity $category
         */
        $category = $this->getCategory($categoryId);

        if (!$category) {
            $this->flashMessage('Kategorie nenalezena.', 'danger');
        }

        $word->removeCategory($category);

        $this->em->persist($word);
        $this->em->flush();

        $this->flashMessage('Kategorie odstraněna.', 'success');

        $this->redrawControl('categories');
        $this->redrawControl('flashes');
    }

    public function createComponentQuickAddCategory() : Form
    {
        $form = new Form();

        $form->addText('name', 'Jméno');

        $form->addSubmit('save', 'Vytvořit kategorii');

        $form->onSuccess[] = [$this, 'quickAddCategorySuccess'];

        $this->doctrineFormMapper->load(CategoryEntity::class, $form);

        return $form;
    }

    public function quickAddCategorySuccess(Form $form) : void
    {
        $wordId = $this->presenter->getParameter('id');

        /**
         * @var CategoryEntity $category
         */
        $category = $this->doctrineFormMapper->save(CategoryEntity::class, $form);

        $category->isActive = true;
        $category->name = ucfirst($category->name);

        $word = $this->getWord($wordId);

        if (!$word) {
            $this->flashMessage('Slovo nenalezeno.', 'danger');
        }

        $category->addWord($word);

        $this->em->persist($category);
        $this->em->flush();

        $this->presenter->redirect('Word:edit', $wordId);
    }
}
