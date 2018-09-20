<?php

namespace SergioTropea\NexiBundle\Controller;

use AppBundle\Entity\Reservation;
use SergioTropea\NexiBundle\Entity\Nexi;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;


class DefaultController extends Controller
{

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request, Nexi $nexi = null)
    {

        //$base_url = $this->container->get('router')->getContext()->getScheme()."://".$request->server->get('REMOTE_ADDR');
        // Alias e chiave segreta
        $CHIAVESEGRETA = $this->container->getParameter('sergio_tropea_nexi.key');

        if ( $this->container->getParameter('sergio_tropea_nexi.environment')  == "production" )
            $requestUrl = "https://ecommerce.nexi.it/ecomm/ecomm/DispatcherServlet";
        else
            $requestUrl = "https://int-ecommerce.nexi.it/ecomm/ecomm/DispatcherServlet";

        $base_url = $this->container->getParameter('sergio_tropea_nexi.url');

        //Dati obbligatori
        $payload['alias'] = $this->container->getParameter('sergio_tropea_nexi.alias');
        $payload['codTrans'] = $nexi->getCodTrans();
        $payload['divisa'] = $nexi->getDivisa();
        $payload['importo'] = $nexi->getImporto();
        $payload['url_back'] = $base_url."/nexi/abort/".$nexi->getId();
        $payload['url'] = $base_url."/nexi/response/".$nexi->getId();
        $payload['mac'] = sha1('codTrans=' . $payload['codTrans'] . 'divisa=' . $payload['divisa'] . 'importo=' . $payload['importo'] . $CHIAVESEGRETA);
        $nexi->setMac($payload['mac']);
        $payload['url_post'] = $base_url."/nexi/server/".$nexi->getId();

        //Aggiornamento Dati Nexi


        //Altri dati facoltativi
        if ($nexi->getMail()) $payload['mail'] = $nexi->getMail();
        if ($nexi->getLanguageId()) $payload['languageId'] = $nexi->getLanguageId();
        if ($nexi->getDescrizione()) $payload['descrizione'] = $nexi->getDescrizione();
        if ($nexi->getOptionCf()) $payload['OPTION_CF'] = $nexi->getOptionCf();
        if ($nexi->getSessionId()) $payload['session_id'] = $nexi->getSessionId();

        $this->getDoctrine()->getManager()->flush();

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

        return $this->render('@SergioTropeaNexi/Default/index.html.twig', array('url' => $redirectUrl));
    }


    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function abortAction(Request $request, Nexi $nexi = null)
    {
        return $this->render('@SergioTropeaNexi/Default/abort.html.twig', array('parameter' => $request->query->all()));
    }


    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function responseAction(Request $request, Nexi $nexi = null)
    {

        if (!$nexi)
            $nexi = $this->getDoctrine()->getRepository('SergioTropeaNexiBundle:Nexi')->findOneBy(array('codTrans' => $_REQUEST['codTrans']));

        $response = $this->get('sergio_tropea_nexi.nexi')->esitoPayment($nexi);

        $referred = explode("-",$nexi->getCodTrans());

        $reservation = $this->getDoctrine()->getRepository('AppBundle:Reservation')->findOneBy(array('referred' => $referred[0]));

//      dump($reservation);die();

        if ($response['esito'] == 'OK') {
            $reservation->setStatus(1);
            $this->getDoctrine()->getManager()->flush();

            //Richiamare la funzione che conferma il pagamento
            $sbc = $this->get('app.soap.sbc');
            $sbc->setModResPay($reservation, $response['nexi']);
        }else{
            $reservation->setStatus(0);
            $this->getDoctrine()->getManager()->flush();
        }

        $nexi->setCodTrans($referred[0]);

        return $this->render('@SergioTropeaNexi/Default/response.html.twig', array('parameter' => $request->query->all(), 'parameter' => $response['nexi'], 'reservation' => $reservation));
    }
}
