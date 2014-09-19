<?php

namespace Fferriere\Bundle\SpreadsheetsReplacementBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('FferriereSpreadsheetsReplacementBundle:Default:index.html.twig', array('name' => $name));
    }
}
