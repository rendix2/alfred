<?php

namespace Alfred\App\WebModule\Presenters;


use Alfred\App\Model\Entity\CategoryEntity;
use Alfred\App\Model\Entity\ChatEntity;
use Alfred\App\Model\Entity\EventEntity;
use Alfred\App\Model\Entity\WordEntity;
use Alfred\App\Model\Entity\WordVariantEntity;
use Alfred\App\WebModule\Components\Word\CategoriesCard;
use Alfred\App\WebModule\Components\Word\ChatCard;
use Doctrine\ORM\QueryBuilder;
use FreezyBee\DoctrineFormMapper\DoctrineFormMapper;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Nettrine\ORM\EntityManagerDecorator;
use Ublaboo\DataGrid\Column\Action\Confirmation\StringConfirmation;
use Ublaboo\DataGrid\DataGrid;

/**
 * class WordPresenter
 *
 * @package Alfred\Presenters
 */
class WordPresenter extends Presenter
{

    private WordEntity $wordEntity;

    public function __construct(
        private EntityManagerDecorator $em,
        private DoctrineFormMapper     $doctrineFormMapper,
    ) {

    }

    public function actionDefault()
    {
    }

    public function actionAdd() : void
    {
    }

    public function actionEdit(int $id) : void
    {
        $this->wordEntity = $this->em->getRepository(WordEntity::class)->find($id);

        if (!$this->wordEntity) {
            $this->flashMessage('Slovo nenalezeno.', 'danger');
        }

        $this->doctrineFormMapper->load($this->wordEntity, $this['form']);
    }

    public function renderEdit(int $id) : void
    {
        $this->template->word = $this->wordEntity;
    }

    public function handleDelete(int $id) : void
    {
        $word = $this->em->getRepository(WordEntity::class)->find($id);

        if (!$word) {
            $this->flashMessage('Slovo nenalezeno.', 'danger');
        }

        try {
            $this->em->remove($word);
            $this->em->flush();

            $message = sprintf('Slovo %s bylo smazáno.', $word->wordText);

            $this->flashMessage($message);

            if ($this->isAjax()) {
                $this->redrawControl('flashes');
                $this['grid']->reload();
            } else {
                $this->redirect('this');
            }
        } catch (\Doctrine\DBAL\Exception $e) {
            $this->flashMessage($e->getMessage(), 'danger');
            $this->redrawControl('flashes');
        }
    }

    public function createComponentCategoriesCard() : CategoriesCard
    {
        return new CategoriesCard($this->em, $this->doctrineFormMapper, $this->wordEntity);
    }

    public function createComponentChatCard() : ChatCard
    {
        return new ChatCard($this->em, $this->doctrineFormMapper, $this->wordEntity);
    }

    public function createComponentGrid(string $name) : DataGrid
    {
        $sep = DIRECTORY_SEPARATOR;

        $dataSource = $this->em
            ->getRepository(WordEntity::class)
            ->createQueryBuilder('_word',);

        $grid = new DataGrid($this, $name);

        $grid->setTemplateFile(__DIR__ . $sep . '..' . $sep . 'templates/Word/grid.latte');
        $grid->setDataSource($dataSource);
        $grid->setDefaultPerPage(100);

        $grid->addColumnNumber('id', 'ID')
            ->setSortable(true)
            ->setFilterText();

        $grid->addColumnText('wordText', 'Slovo')
            ->setEditableCallback(
                function ($id, string $value) : void {
                    /**
                     * @var WordEntity $word
                     */
                    $word = $this->em->getRepository(WordEntity::class)->find($id);
                    $word->wordText = $value;

                    $this->em->persist($word);
                    $this->em->flush();
                }
            )
            ->setSortable(true)
            ->setFilterText();

        $tempVariants = $this->em
            ->getRepository(WordVariantEntity::class)
            ->createQueryBuilder('_variant')
            ->orderBy('_variant.variantText', 'ASC')
            ->getQuery()
            ->getResult();

        $variants = [null => 'Vyberte'];

        foreach ($tempVariants as $tempVariant) {
            $variants[$tempVariant->id] = $tempVariant->variantText;
        }

        $grid->addColumnText('variants', 'Varianty')
            ->setFilterSelect($variants)
            ->setCondition(
                function (QueryBuilder $queryBuilder, int|string|array $variants) {
                    if (is_int($variants)) {
                        $variants = [$variants];
                    } elseif (is_string($variants)) {
                        $variants = explode(',', $variants);
                    }

                    $queryBuilder
                        ->addSelect('_variants')
                        ->leftJoin('_word.variants', '_variants')
                        ->andWhere('_variants.id IN (:variants)')
                        ->setParameter('variants', $variants);
                }
            );

        $grid->addColumnText('description', 'Popis')
            ->setEditableCallback(
                function ($id, string $description) : void {
                    $word = $this->em->getRepository(WordEntity::class)->find($id);
                    $word->wordText = $description;

                    $this->em->persist($word);
                    $this->em->flush();
                }
            )
            ->setSortable(true)
            ->setFilterText();

        $tempCategories = $this->em
            ->getRepository(CategoryEntity::class)
            ->createQueryBuilder('_category')
            ->orderBy('_category.name', 'ASC')
            ->getQuery()
            ->getResult();

        $categories = [null => 'Vyberte'];

        foreach ($tempCategories as $tempCategory) {
            $categories[$tempCategory->id] = $tempCategory->name;
        }

        $grid->addColumnText('categories', 'Kategorie')
            ->setFilterSelect($categories)
            ->setCondition(
                function (QueryBuilder $queryBuilder, int|string|array $categories) {
                    if (is_int($categories)) {
                        $categories = [$categories];
                    } elseif (is_string($categories)) {
                        $categories = explode(',', $categories);
                    }

                    $queryBuilder
                        ->addSelect('_categories')
                        ->leftJoin('_word.categories', '_categories')
                        ->andWhere('_categories.id IN (:categories)')
                        ->setParameter('categories', $categories);
                }
            );


        $grid->addColumnText('requests', 'Požadavky')/*->setFilterSelect($requests)
            ->setCondition(
                function (QueryBuilder $queryBuilder, int|string|array $requests) {
                    if (is_int($requests)) {
                        $requests = [$requests];
                    } elseif (is_string($requests)) {
                        $requests = explode(',', $requests);
                    }

                    $queryBuilder
                        ->addSelect('_requests')
                        ->addSelect('_chat')
                        ->leftJoin('_word.requests', '_requests')
                        ->leftJoin('_requests.chat', '_chat')
                        ->andWhere('_chat.id IN (:chats)')
                        ->setParameter('chats', $requests);
                }
            )*/
        ;


        $tempChats = $this->em
            ->getRepository(ChatEntity::class)
            ->createQueryBuilder('_chat')
            ->orderBy('_chat.name', 'ASC')
            ->getQuery()
            ->getResult();

        $chats = [null => 'Vyberte'];

        foreach ($tempChats as $tempChat) {
            $chats[$tempChat->id] = $tempChat->name;
        }

        $grid->addColumnText('chats', 'Chaty')
            ->setFilterSelect($chats)
            ->setCondition(
                function (QueryBuilder $queryBuilder, int|string|array $chats) {
                    if (is_int($chats)) {
                        $chats = [$chats];
                    } elseif (is_string($chats)) {
                        $chats = explode(',', $chats);
                    }

                    $queryBuilder
                        ->addSelect('_requests')
                        ->addSelect('_chat')
                        ->leftJoin('_word.requests', '_requests')
                        ->leftJoin('_requests.chat', '_chat')
                        ->andWhere('_chat.id IN (:chats)')
                        ->setParameter('chats', $chats);
                }
            );

        $tempEvents = $this->em
            ->getRepository(EventEntity::class)
            ->createQueryBuilder('_event')
            ->orderBy('_event.name', 'ASC')
            ->getQuery()
            ->getResult();

        $events = [null => 'Vyberte'];

        foreach ($tempEvents as $tempEvent) {
            $events[$tempEvent->id] = $tempEvent->name;
        }

        $grid->addColumnText('events', 'Události')
            ->setFilterSelect($events)
            ->setCondition(
                function (QueryBuilder $queryBuilder, int|string|array $events) {
                    if (is_int($events)) {
                        $events = [$events];
                    } elseif (is_string($events)) {
                        $events = explode(',', $events);
                    }

                    $queryBuilder
                        ->addSelect('_requests')
                        ->addSelect('_event')
                        ->leftJoin('_word.requests', '_requests')
                        ->leftJoin('_requests.event', '_event')
                        ->andWhere('_event.id IN (:events)')
                        ->setParameter('events', $events);
                }
            );

        $grid->addColumnDateTime('createdAt', 'Vytvořeno')
            ->setSortable(true)
            ->setFilterDate();

        $grid->addColumnDateTime('updatedAt', 'Aktualizováno')
            ->setSortable(true)
            ->setFilterDate();

        $grid->addAction('edit', 'Editovat')
            ->setIcon('edit');

        $grid->addAction('delete', 'Smazat', 'delete!')
            ->setIcon('trash')
            ->setTitle('Smazat')
            ->setConfirmation(
                new StringConfirmation('Opravdu chcete smazat Slovo %s?', 'wordText') // Second parameter is optional
            );


        return $grid;
    }

    public function createComponentForm() : Form
    {
        $form = new Form();

        $form->addText('wordText', 'Slovo')
            ->setRequired('Zadejte prosím slovo.');

        $form->addTextArea('description', 'Popis')
            ->setNullable(true)
            ->setRequired(false);

        $form->addSubmit('send', 'Uložit Slovo');

        $this->doctrineFormMapper->load(WordEntity::class, $form);

        $form->onSuccess[] = [$this, 'formSuccess'];

        return $form;
    }

    public function formSuccess(Form $form) : void
    {
        $id = $this->getParameter('id');

        if ($id) {
            $input = $this->em->getRepository(WordEntity::class)->find($id);
        } else {
            $input = WordEntity::class;
        }

        $word = $this->doctrineFormMapper->save($input, $form);

        $this->em->persist($word);
        $this->em->flush();

        $this->flashMessage('Slovo bylo uloženo.', 'success');
        $this->redirect('Word:edit', $word->id);
    }
}
