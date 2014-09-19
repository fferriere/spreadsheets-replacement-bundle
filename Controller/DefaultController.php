<?php

namespace Fferriere\Bundle\SpreadsheetsReplacementBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function replaceAction(Request $request)
    {
        $dataPath = rtrim($this->container->getParameter('fferriere_spreadsheets_replacement.data_path'), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        $form = $this->createFormBuilder()
                    ->add('file', 'file', array('label' => 'Fichier :'))
                    ->add('submit', 'submit', array('label' => 'Valider'))
                    ->getForm();

        $form->handleRequest($request);

        if($form->isValid()) {
            if(!is_dir($dataPath)) {
                mkdir($dataPath, 0770, true);
            }
            $newFileName = date('Y-m-d-His') . '.csv';

            $form['file']->getData()->move($dataPath, $newFileName);

            $filepath = $dataPath . $newFileName;

            $replacer = $this->container->get('fferriere_spreadsheets_replacement.replacer');
            if(! $replacer instanceof \Fferriere\SpreadsheetsReplacement\Replacer\CsvReplacer) {
                throw new Exception('$replacer is not an instance of Fferriere\SpreadsheetsReplacement\Replacer\CsvReplacer');
            }

            $sheet = $replacer->getSheet();
            if($sheet instanceof \Fferriere\SpreadsheetsReplacement\Sheet\CsvSheet) {
                $sheet->setReadFilePath($filepath);
            }

            $hydrator = $this->container->get('fferriere_spreadsheets_replacement.hydrator');
            if(!$hydrator instanceof \Fferriere\SpreadsheetsReplacement\Hydrator\HydratorInterface) {
                throw new Exception('$hydrator is not an instance of Fferriere\SpreadsheetsReplacement\Hydrator\HydratorInterface');
            }

            $params = include $this->container->getParameter('fferriere_spreadsheets_replacement.replacement_pattern_path');

            $columns = $hydrator->hydrate($params);
            $sheet->addColumns($columns);

            $newFilepath = $replacer->replaceFile();
            $filename = basename($newFilepath);

            $response = new \Symfony\Component\HttpFoundation\Response();
            $response->setStatusCode(200);
            $response->headers->set('Content-Type', 'text/csv');
            $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');
            $response->headers->set('Content-Length', filesize($newFilepath));
            $response->setContent(file_get_contents($newFilepath));
            return $response;
        }

        return $this->render('FferriereSpreadsheetsReplacementBundle:Default:replace.html.twig',
                array(
                    'form' => $form->createView()
                )
            );
    }
}
