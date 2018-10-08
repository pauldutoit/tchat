<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Message;
use App\Form\MessageType;
use App\Repository\UserRepository;
use App\Repository\MessageRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ConversationController extends AbstractController
{
    /**
     * @Route("/conversations", name="conversation_conversations")
     */
    public function conv()
    {
        $users = $this->getUserList();
        return $this->render('conversation/conversations.html.twig',[
            'users' => $users
        ]);
    }


    /**
     * @Route("/conversation/{id}/message", name="conversation_conversationTo")
     */
    public function message(Request $request, ObjectManager $manager, $id)
    {
        $message = new Message();
        $users = $this->getUserList();
        $envoyeur = $this->getUser()->getUsername();
        $destinataire = $this->getDoctrine()->getRepository(User::class)->find($id)->getUsername();
        $messages = $this->getMessageList($envoyeur, $destinataire);
        $form = $this->createForm(MessageType::class, $message);
        
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $message->setEnvoyeur($envoyeur);
            $message->setDestinataire($destinataire);
            $message->setCreatedAt(new \Datetime());
            $message->setReadAt(new \Datetime());

            $manager->persist($message);
            $manager->flush();            
        }

        return $this->render('conversation/conversationTo.html.twig',[
            'id' => $id,
            'users' => $users,
            'destinataire' => $destinataire,
            'form' => $form->createView(),
            'messages' => $messages
        ]);

    }

    public function getUserList(){
        $repository = $this->getDoctrine()->getRepository(User::class);
        $users = $repository->findAll();

        return $users;
    }

    public function getMessageList($envoyeur, $destinataire){
        $repository = $this->getDoctrine()->getRepository(Message::class);
        $messages = $repository->findMessagesByExpediteurAndDestinataire($envoyeur, $destinataire);
        
        return $messages;
    }

}
