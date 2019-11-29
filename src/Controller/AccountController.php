<?php

declare(strict_types = 1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\AccountRepository;
use App\Entity\Account;
use App\Form\AccountType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Doctrine\ORM\EntityManagerInterface;

class AccountController extends AbstractController
{
    /**
     * @Route("/accounts", name="account")
     */
    public function index(AccountRepository $repository): Response
    {
        $accounts = $repository->findAll();

        return $this->render('account/index.html.twig', [
            'accounts' => $accounts,
        ]);
    }

    /**
     * @Route("/accounts/{id}", name="account_view", requirements={"id"="\d+"})
     */
    public function view(Account $account): Response
    {
        return $this->render('account/view.html.twig', [
            'account' => $account,
        ]);
    }

    /**
     * @Route("/accounts/new", name="account_add")
     */
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $account = new Account();
        $form = $this->get('form.factory')
            ->createBuilder(AccountType::class, $account)
            ->add('submit', SubmitType::class, [
                'label' => 'Add',
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($account);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Account successfully created.'
            );
            return $this->redirectToRoute('account');
        }

        return $this->render('account/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/accounts/{id}/edit", name="account_edit")
     */
    public function edit(Account $account, Request $request, EntityManagerInterface $entityManager)
    {
        $form = $this->get('form.factory')
            ->createBuilder(AccountType::class, $account)
            ->add('submit', SubmitType::class, [
                'label' => 'Save',
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash(
                'success',
                $account->getName() . ' has been modified.'
            );
            return $this->redirectToRoute('account');
        }

        $deleteForm = $this->createFormBuilder()
            ->setAction($this->generateUrl('account_delete', ['id' => $account->getId()]))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class, [
                'label' => 'Delete',
                'attr' => [
                    'onclick' => 'return confirm("Are you sure you want to delete ' . $account->getName() . '? This action cannot be undone.")',
                ]
            ])
            ->getForm();

        return $this->render('account/new.html.twig', [
            'form' => $form->createView(),
            'deleteForm' => $deleteForm->createView(),
            'account' => $account,
        ]);
    }

    /**
     * @Route(
     *     "/accounts/{id}/delete",
     *     name="account_delete",
     *     methods={"DELETE"}
     * )
     */
    public function delete(Account $account, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($account);
        $entityManager->flush();
        return $this->redirectToRoute('account');
    }
}
