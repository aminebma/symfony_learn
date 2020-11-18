<?php


namespace App\Controller;

use App\Document\Todo;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class todoController extends AbstractController
{
    /**
     * @Route("/todo/add", methods={"POST"}, name="create_todo")
     */
    public function createTodo(Request $request, DocumentManager $dm)
    {
        date_default_timezone_set('Africa/Algiers');
        $parameters = json_decode($request->getContent(), true);
        $todo = new Todo();
        $todo->setDate($parameters['date']);
        $todo->setNote($parameters['note']);

        $dm->persist($todo);
        $dm->flush();


        return new Response('Created product id ' . $todo->getId());
    }

    /**
     * @Route("/todo/{id}", methods={"GET"}, name="get_todo")
     */
    public function getTodo(DocumentManager $dm, string $id): Response
    {
        $todo = $dm->getRepository(Todo::class)->find($id);

        if (!$todo) {
            throw $this->createNotFoundException('No todo found for id ' . $id);
        }

        return $this->render('todo/todo.html.twig', [
            'date' => $todo->getDate()->format('d.m.y'),
            'note' => $todo->getNote(),
        ]);
    }

    /**
     * @Route("/todos/all", methods={"GET"}, name="get_all_todos")
     */
    public function getAllTodos(DocumentManager $dm) : Response
    {
        $todos = $dm->getRepository(Todo::class)->findAll();

        if (!$todos)
            throw $this->createNotFoundException('No todos found');

        $response = new JsonResponse();
        $response->setContent(json_encode($todos));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }


    /**
     * @Route("/todo/update", methods={"POST"}, name="update_todo")
     */
    public function updateTodo(Request $request, DocumentManager $dm): Response{
        $parameters = json_decode($request->getContent(), true);
        $todo = $dm->getRepository(Todo::class)->find($parameters['id']);

        if(!$todo)
            throw $this->createNotFoundException('No todo found for id ' . $parameters['id']);

        $todo->setNote($parameters['note']);
        $todo->setDate($parameters['date']);

        $dm->flush();

        return new Response("To do updated. id: " . $todo->getId());
    }

    /**
     * @Route("/todo/delete/{id}", methods={"DELETE"}, name="delete_todo")
     */
    public function deleteTodo(DocumentManager $dm, string $id) : Response
    {
        $todo = $dm->getRepository(Todo::class)->find($id);
        if (!$todo) {
            throw $this->createNotFoundException('No todo found for id ' . $id);
        }

        $dm->remove($todo);
        $dm->flush();

        return new Response("Todo deleted succeslly. id " . $id);
    }
}