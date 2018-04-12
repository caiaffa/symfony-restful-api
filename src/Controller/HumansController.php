<?php

namespace App\Controller;

use App\Controller\Pagination\Pagination;
use App\Entity\Person;
use FOS\RestBundle\Controller\ControllerTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use App\Exception\ValidationException;

class HumansController extends AbstractController
{
    use ControllerTrait;

    /**
     * @var Pagination
     */
    private $pagination;

    /**
     * HumansController constructor.
     * @param Pagination $pagination
     */
    public function __construct(Pagination $pagination)
    {
        $this->pagination = $pagination;
    }

    /**
     * @Rest\View()
     */
    public function getHumansAction(Request $request)
    {
        return $this->pagination->paginate(
            $request,
            'App:Person',
            [],
            'findCount',
            [],
            'get_humans',
            []
        );
    }

    /**
     * @Rest\View(statusCode=201)
     * @ParamConverter("person", converter="fos_rest.request_body")
     * @Rest\NoRoute()
     */
    public function postHumansAction(Person $person, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
            throw new ValidationException($validationErrors);
        }
        $manager = $this->getDoctrine()->getManager();

        $manager->persist($person);
        $manager->flush();

        return $person;
    }

    /**
     * @Rest\View()
     */ 
    public function deleteHumanAction(?Person $person) 
    {
        if (null === $person) {
            return $this->view(null, 404);
        }

        $manager = $this->getDoctrine()->getManager();
        $manager->remove($person);
        $manager->flush();
    }

    /**
     * @Rest\View()
     */
    public function getHumanAction(?Person $person)
    {
        if (null === $person) {
            return $this->view(null, 404);
        }

        return $person;
    }
}
