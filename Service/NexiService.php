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
        $payload['url_back'] = $base_url."/nexi/esito/abort"; // .$nexi->getId();
        $payload['url'] = $base_url."/nexi/esito/response"; //.$nexi->getId();
        $payload['mac'] = sha1('codTrans=' . $payload['codTrans'] . 'divisa=' . $payload['divisa'] . 'importo=' . $payload['importo'] . $this->key);
        $nexi->setMac($payload['mac']);
        $payload['url_post'] = $base_url."/nexi/esito/server"; //.$nexi->getId();


        //Altri dati facoltativi
        if ($nexi->getMail()) $payload['mail'] = $nexi->getMail();
        if ($nexi->getLanguageId()) $payload['languageId'] = $nexi->getLanguageId();
        if ($nexi->getDescrizione()) $payload['descrizione'] = $nexi->getDescrizione();
        if ($nexi->getOptionCf()) $payload['OPTION_CF'] = $nexi->getOptionCf();
        if ($nexi->getSessionId()) $payload['session_id'] = $nexi->getSessionId();

        //Verifico se esiste sul database un pagamento precedentemente inserito
        //Creazione dell'oggetto e Aggiornamento Dati Nexi
        if ( !$this->entityManager->getRepository('SergioTropeaNexiBundle:Nexi')->findOneBy(['codTrans' => $nexi->getCodTrans()]) )
            $this->entityManager->persist($nexi);
        $this->entityManager->flush();

        $aRequestParams = array();
        foreach ($payload as $param => $value) {
            $aRequestParams[] = $param . "=" . $value;
        }

        $stringRequestParams = implode("&", $aRequestParams);

        $redirectUrl = $requestUrl . "?" . $stringRequestParams;

        return array('url' => $redirectUrl, 'nexi' => $nexi);
    }

    /**
     * @param Nexi $nexi
     *
     * Parametri Obbligatori
     * alias	Codice identificativo del profilo esercente (valore fisso comunicato da Nexi nella fase di attivazione)	AN MAX 30
     * importo	Importo da autorizzare espresso in centesimi di euro senza separatore, i primi 2 numeri a destra rappresentano gli euro cent, es.: 5000 corrisponde a 50,00 €	N MAX 8
     * divisa	Il codice della divisa in cui l'importo è espresso unico valore ammesso: EUR (Euro)	AN MAX 3
     * codTrans	Codice di identificazione del pagamento composto da caratteri alfanumerici, escluso il carattere #. Il codice dev'essere univoco per ogni richiesta di autorizzazione, solo in caso di esito negativo dell'autorizzazione il merchant può riproporre la stessa richiesta con medesimo codTrans per altre 2 volte, in fase di configurazione l'esercente può scegliere di diminuire i 3 tentativi	AN MIN 2 MAX 30
     * Escluso carattere #. In caso di attivazione del servizio MyBank, i soli caratteri speciali utilizzabili sono: / - : ( ) . , +
     * brand	Tipo di carta utilizzata dall'utente per eseguire il pagamento.I valori possibili sono quelli riportati nella tabella Codifica tipo carta.	AN MAX 100
     * mac	Message Code Authentication Campo di firma della transazione. Per il calcolo si vedano le indicazioni in calce a questo capitolo: Calcolo MAC	AN 40 CRT
     * esito	Esito dell'operazione (Valori possibili OK, KO, ANNULLO e ERRORE)	AN MAX 7
     * data	Data della transazione	DATA MAX 8 aaaammgg
     * orario	Ora della transazione	AN MAX 6 hhmmss
     * codiceEsito	Esito della transazione. I valori possibili sono quelli riportati nella tabella Codifica codiceEsito e descrizioneEsito.	N MAX 3
     * codAut	Codice dell'autorizzazione assegnato dall'emittente della carta di credito, presente solo con autorizzazione concessa
     * pan	Numero carta di credito mascherato in chiaro solo le prime 6 e ultime 4 cifre	AN MAX 100
     * scadenza_pan	Scadenza carta di credito
     * regione Se abilitato viene restituito la macroregione di appartenenza della carta usata per il pagamento (es.: Europa)	AN MAX 30
     * nazionalita Riporta la nazionalità della carta che ha eseguito il pagamento	AN MIN 3 MAX 3 Codifica ISO 3166-1 alpha-3
     * descrizione Campo in cui il merchant può specificare una descrizione del tipo di servizio offerto. Questo campo verrà riportato anche nel testo della mail inviata al cardholder. Per il servizio MyBank il campo viene veicolato alla banca per essere inserito nella descrizione della disposizione SCT ma viene troncato al 140mo carattere	AN MAX 2000
     * Per MyBank: AN MAX 140 CRT e i soli caratteri speciali utilizzabili sono: / - : ( ) . , +
     * languageId Identificativo della lingua che verrà visualizzata sulla pagina di cassa; le lingue disponibili sono quelle riportate nella tabella Codifica languageId. Se ale campo non viene specificato o viene lasciato vuoto verranno visualizzati i testi secondo quando definito come default in fase di configurazione del servizio	AN MAX 7
     * tipoTransazione Tipo di transazione, indica la modalità con cui è avvenuto il pagamento, per i possibili valori vedere la tabella Codifica tipo Transazione. In caso di pagamento con esito negativo sarà spedita una stringa vuota	AN MAX 20
     * tipoProdotto Se abilitato viene restituito la descrizione del tipo carta usata per il pagamento (es.: consumer)	AN MAX 30
     * nome Nome di chi ha effettuato il pagamento	AN MAX 150
     * cognome Cognome di chi ha effettuato il pagamento	AN MAX 150
     * mail L'indirizzo e-mail dell'acquirente al quale inviare l'esito del pagamento	AN MAX 150
     * session_id Identificativo della sessione	AN MAX 100
     * messaggio Riporta una breve descrizione dell'esito del pagamento. I valori possibili sono quelli riportati nella tabella Codifica esiti
     *
     * Parametri Facoltativi
     * hash	Se previsto dal profilo dell'esercente viene restituito questo campo valorizzato con l'hash del PAN della carta utilizzata per il pagamento	AN MAX 28
     * infoc	Informazione aggiuntiva relativa al singolo pagamento. Tale informazione può essere veicolata alla compagnia in base ad accordi preventivi con la compagnia stessa	AN MAX 35
     * infob	Informazione aggiuntiva relativa al singolo pagamento. Tale informazione può essere veicolata alla banca in base ad accordi preventivi con la banca stessa	AN MAX 20
     * codiceConvenzione	Codice esercente assegnato dall'acquirer. Dove previsto	AN MAX 15
     * modo_gestione_consegna	Campo disponibile solo per pagamenti tramite wallet MySi in base alla sua valorizzazione nell'esito saranno riportati i dettagli del cliente. Possibili valori:
     *                          - no: nessun valore restituito
     *                          - mail_tel: prevede la restituzione dell'indirizzo mail,
     *                          - telefono e indirizzo di fatturazionecompleto: prevede la restituzione dell'indirizzo mail, telefono, indirizzo di fatturazione e indirizzo di spedizione	AN MAX 40
     * dati_gestione_consegna	Xml con dati di spedizione. Di seguito riportiamo la struttura dell'XML	AN MAX 700
     */
    public function esitoPayment(Nexi $nexi){

        //localhost:8000/nexi/esito/response?mail=assistenza%40edigma.it&data=20180619&messaggio=Message+OK&cognome=Rossi&nazionalita=ITA&regione=&mac=d179c04e7265280624a3256ea8e91732a8ebe0e3&codAut=NMS76B&tipoProdotto=VISA+CLASSIC+-+CREDIT+-+N&alias=ALIAS_WEB_00003045&pan=453997XXXXXX0006&brand=VISA&orario=105002&divisa=EUR&scadenza_pan=203012&importo=4147&codiceEsito=0&languageId=ITA&session_id=6cfd59319dbfaafda03017de4d9aea6f1d82d3fa79320547&nome=Mario&check=&tipoTransazione=VBV_FULL&codiceConvenzione=00003045104&codTrans=583071114&tipo_richiesta=PA&esito=OK&TCONTAB=&OPTION_CF=&num_contratto=

        // Chiave segreta
        //$CHIAVESEGRETA = "<CHIAVE SEGRETA PER CALCOLO MAC>"; // Sostituire con il valore fornito da Nexi
        $msg ="";
        // Controllo che ci siano tutti i parametri di ritorno obbligatori per calcolare il MAC
        $requiredParams = array('codTrans', 'esito', 'importo', 'divisa', 'data', 'orario', 'codAut', 'mac');
        foreach ($requiredParams as $param) {
            if (!isset($_REQUEST[$param])) {
                $msg = 'Paramentro mancante ' . $param;
                $esito = 'KO';
                return array('nexi' => $nexi, "msg" => $msg, 'esito' => $esito);
            }
        }

        // Calcolo MAC con i parametri di ritorno
        $macCalculated = sha1('codTrans=' . $_REQUEST['codTrans'] .
            'esito=' . $_REQUEST['esito'] .
            'importo=' . $_REQUEST['importo'] .
            'divisa=' . $_REQUEST['divisa'] .
            'data=' . $_REQUEST['data'] .
            'orario=' . $_REQUEST['orario'] .
            'codAut=' . $_REQUEST['codAut'] .
            $this->key
        );

        // Verifico corrispondenza tra MAC calcolato e parametro mac di ritorno
        if ($macCalculated != $_REQUEST['mac']) {
            echo 'Errore MAC: ' . $macCalculated . ' non corrisponde a ' . $_REQUEST['mac'];
            exit;
        }

        //Altri Parametri
        $codiceEsito = $_REQUEST['codiceEsito'];
        $brand = $_REQUEST['brand'];
        $messaggio = $_REQUEST['messaggio'];

        //Aggiornamento Oggetto Nexi
        $nexi->setCodiceAut($_REQUEST['codAut']);
        $nexi->setEsito($_REQUEST['esito']);
        $nexi->setDataPagamento($_REQUEST['data']);
        $nexi->setOrario($_REQUEST['orario']);
        $nexi->setMessaggio($_REQUEST['messaggio']);
        $nexi->setBrand($_REQUEST['brand']);
        $this->entityManager->flush();

        $esito=$_REQUEST['esito'];
        // Nel caso in cui non ci siano errori gestisco il parametro esito
        if ($_REQUEST['esito'] == 'OK') {
            $msg = 'La transazione ' . $_REQUEST['codTrans'] . " è avvenuta con successo; codice autorizzazione: " . $_REQUEST['codAut'];
        } else {
            $msg = 'La transazione ' . $_REQUEST['codTrans'] . " è stata rifiutata; descrizione errore: " . $_REQUEST['messaggio'];
        }

        return
            array(
                'nexi' => $nexi,
                "msg" => $msg,
                'esito' => $esito,
                'codiceEsito' => $codiceEsito,
                'messaggio' => $codiceEsito,
            );
    }
}