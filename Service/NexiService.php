<?php

namespace SergioTropea\NexiBundle\Service;

use Doctrine\ORM\EntityManager;
use SergioTropea\NexiBundle\Entity\Nexi;

class NexiService{

    private $entityManager;
    private $url;
    private $alias;
    private $environment;
    private $key;
    private $rootDir;

    public function __construct(EntityManager $entityManager, $url, $alias, $environment, $key, $rootDir)
    {
        $this->entityManager = $entityManager;
        $this->url = $url;
        $this->alias = $alias;
        $this->environment = $environment;
        $this->key = $key;
        $this->rootDir = $rootDir;
    }


    public function generateUrl(Nexi $nexi){

            if ( $this->environment  == "production" )
                $requestUrl = "https://ecommerce.nexi.it/ecomm/ecomm/DispatcherServlet";
            else
                $requestUrl = "https://int-ecommerce.nexi.it/ecomm/ecomm/DispatcherServlet";

            $base_url = $this->url;

            //Dati obbligatori
            $payload['alias'] = $this->alias;
            $payload['codTrans'] = $nexi->getCodTrans();
            $payload['divisa'] = $nexi->getDivisa();
            $payload['importo'] = $nexi->getImporto();
            $payload['url_back'] = $base_url."/nexi/abort/".$nexi->getId();
            $payload['url'] = $base_url."/nexi/response/".$nexi->getId();
            $payload['mac'] = sha1('codTrans=' . $payload['codTrans'] . 'divisa=' . $payload['divisa'] . 'importo=' . $payload['importo'] . $this->key);
            $nexi->setMac($payload['mac']);
            $payload['url_post'] = $base_url."/nexi/server/".$nexi->getId();


            //Altri dati facoltativi
            if ($nexi->getMail()) $payload['mail'] = $nexi->getMail();
            if ($nexi->getLanguageId()) $payload['languageId'] = $nexi->getLanguageId();
            if ($nexi->getDescrizione()) $payload['descrizione'] = $nexi->getDescrizione();
            if ($nexi->getOptionCf()) $payload['OPTION_CF'] = $nexi->getOptionCf();
            if ($nexi->getSessionId()) $payload['session_id'] = $nexi->getSessionId();

            //Creazione dell'oggetto e Aggiornamento Dati Nexi
            $this->entityManager->persist($nexi);
            $this->entityManager->flush();

            // Calcolo MAC
            // Parametri obbligatori
            /*
            $obbligatori = array(
                'alias' => $ALIAS,
                'importo' => $importo,
                'divisa' => $divisa,
                'codTrans' => $codTrans,
                'url' => $url_response,
                'url_back' => $url_back,
                'mac' => $mac,
            );

            // Parametri facoltativi
            $facoltativi = array(
                'mail' => $mail,
                'languageId' => $languageId,
                'url_post' => $url_post,
                'descrizione' => $descrizione,
                'session_id' => $session_id,
                'OPTION_CF' => $OPTION_CF,
            );
            */

            $aRequestParams = array();
            foreach ($payload as $param => $value) {
                $aRequestParams[] = $param . "=" . $value;
            }

            $stringRequestParams = implode("&", $aRequestParams);

            $redirectUrl = $requestUrl . "?" . $stringRequestParams;

            return array('url' => $redirectUrl, 'nexi' => $nexi);
    }
}