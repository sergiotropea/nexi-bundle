<?php

namespace SergioTropea\NexiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Nexi
 *
 * @ORM\Table(name="nexi")
 * @ORM\Entity(repositoryClass="SergioTropea\NexiBundle\Repository\NexiRepository")
 */
class Nexi
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="cod_trans", type="string", length=255, unique=true)
     */
    private $codTrans;

    /**
     * @var string
     *
     * @ORM\Column(name="divisa", type="string", length=255)
     */
    private $divisa;

    /**
     * @var string
     *
     * @ORM\Column(name="importo", type="string", length=255)
     */
    private $importo;

    /**
     * @var string
     *
     * @ORM\Column(name="mail", type="string", length=255, nullable=true)
     */
    private $mail;

    /**
     * @var int
     *
     * @ORM\Column(name="language_id", type="string", length=3, nullable=true)
     */
    private $languageId;

    /**
     * @var string
     *
     * @ORM\Column(name="descrizione", type="string", length=255, nullable=true)
     */
    private $descrizione;

    /**
     * @var string
     *
     * @ORM\Column(name="option_cf", type="string", length=255, nullable=true)
     */
    private $optionCf;

    /**
     * @var string
     *
     * @ORM\Column(name="session_id", type="string", length=255, nullable=true)
     */
    private $sessionId;


    /**
     * @var string
     *
     * @ORM\Column(name="mac", type="string", length=255, nullable=true)
     */
    private $mac;




    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set codTrans.
     *
     * @param string $codTrans
     *
     * @return Nexi
     */
    public function setCodTrans($codTrans)
    {
        $this->codTrans = $codTrans;

        return $this;
    }

    /**
     * Get codTrans.
     *
     * @return string
     */
    public function getCodTrans()
    {
        return $this->codTrans;
    }

    /**
     * Set divisa.
     *
     * @param string $divisa
     *
     * @return Nexi
     */
    public function setDivisa($divisa)
    {
        $this->divisa = $divisa;

        return $this;
    }

    /**
     * Get divisa.
     *
     * @return string
     */
    public function getDivisa()
    {
        return $this->divisa;
    }

    /**
     * Set importo.
     *
     * @param string $importo
     *
     * @return Nexi
     */
    public function setImporto($importo)
    {
        $this->importo = $importo;

        return $this;
    }

    /**
     * Get importo.
     *
     * @return string
     */
    public function getImporto()
    {
        return $this->importo;
    }


    /**
     * Set mail.
     *
     * @param string $mail
     *
     * @return Nexi
     */
    public function setMail($mail)
    {
        $this->mail = $mail;

        return $this;
    }

    /**
     * Get mail.
     *
     * @return string
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * Set languageId.
     *
     * @param int $languageId
     *
     * @return Nexi
     */
    public function setLanguageId($languageId)
    {
        $this->languageId = $languageId;

        return $this;
    }

    /**
     * Get languageId.
     *
     * @return int
     */
    public function getLanguageId()
    {
        return $this->languageId;
    }

    /**
     * Set descrizione.
     *
     * @param string $descrizione
     *
     * @return Nexi
     */
    public function setDescrizione($descrizione)
    {
        $this->descrizione = $descrizione;

        return $this;
    }

    /**
     * Get descrizione.
     *
     * @return string
     */
    public function getDescrizione()
    {
        return $this->descrizione;
    }

    /**
     * Set optionCf.
     *
     * @param string $optionCf
     *
     * @return Nexi
     */
    public function setOptionCf($optionCf)
    {
        $this->optionCf = $optionCf;

        return $this;
    }

    /**
     * Get optionCf.
     *
     * @return string
     */
    public function getOptionCf()
    {
        return $this->optionCf;
    }

    /**
     * Set sessionId.
     *
     * @param string $sessionId
     *
     * @return Nexi
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;

        return $this;
    }

    /**
     * Get sessionId.
     *
     * @return string
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }


    /**
     * Set mac.
     *
     * @param string $mac
     *
     * @return Nexi
     */
    public function setMac($mac)
    {
        $this->mac = $mac;

        return $this;
    }

    /**
     * Get mac.
     *
     * @return string
     */
    public function getMac()
    {
        return $this->mac;
    }

}
