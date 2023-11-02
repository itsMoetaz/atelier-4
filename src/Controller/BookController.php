<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    #[Route('/book', name: 'app_book')]
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }
    #[Route('/afficher', name:'afficher')]
    public function fetch(BookRepository $repo)
    {$result=$repo->findAll();
    return $this->render('book/afficherbook.html.twig',['response'=>$result]);
    
    }
    #[Route('/addbook',name:'add')]
    public function add(ManagerRegistry $mr,BookRepository $rep,Request $req):Response
    {   
        $s=new Book();//1 instance    update 
        $form=$this->createForm(BookType::class,$s);
        $form->handleRequest($req);
        if($form->isSubmitted()){
      $s->setPublished(true);
      $author = $s->getAuthor();
    $author->setNb_books($author->getNb_books()+1);
        $em=$mr->getManager();//3 persist+flush
        $em->persist($s);
        $em->flush();
        return $this->redirectToRoute('afficher');   }
        return $this->render('book/ajouterbook.html.twig',[
            'f'=>$form->createView() //update ttbadel
        ]);     
}
#[Route('/afficherpublish', name: 'afficherpublish')]
public function fetchpublished(BookRepository $repo): Response
{
    $result = $repo->findBy(['published' => 1]);
    
    return $this->render('book/afficherbook.html.twig', ['response' => $result]);
}
#[Route('/show/{ref}', name: 'showbook')]
public function authordetail( Book $book): Response
    {  
        return $this->render('book/showbook.html.twig', ['book' => $book]);
    }
#[Route('/update/{ref}',name:'update')]
public function modif(int $ref,Request $req,EntityManagerInterface $em):Response

{
    $Book = $em->getRepository(Book::class)->find($ref);

    $form = $this->createForm(BookType::class, $Book);
    $form->handleRequest($req);
    if ($form->isSubmitted() && $form->isValid()) {
        $em->flush();

        return $this->redirectToRoute('afficher');
    }

    return $this->render('book/update.html.twig', [
        'f' => $form->createView(),
    ]);}
    #[Route('/deletebook/{ref}', name: 'deletebook')]
    public function delete(Book $book, ManagerRegistry $mr): Response
    {
        $em = $mr->getManager();
        $em->remove($book);
        $em->flush();
        return $this->redirectToRoute('afficher');
    }


}