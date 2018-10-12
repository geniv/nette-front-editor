<?php declare(strict_types=1);

namespace FrontEditor;

use Identity\IIdentityModel;
use Nette\Http\Session;
use Nette\Http\SessionSection;


/**
 * Trait FrontEditorControl
 *
 * @author  geniv
 * @package FrontEditor
 */
trait FrontEditorControl
{
    /** @var IIdentityModel */
    private $_identityModel;
    /** @var SessionSection */
    private $_section;
    /** @var string */
    private $_sessionExpire;


    /**
     * FrontEditorControl constructor.
     *
     * @param IIdentityModel $identityModel
     * @param Session        $session
     */
    public function __construct(IIdentityModel $identityModel, Session $session)
    {
        $this->_identityModel = $identityModel;
        $this->_section = $session->getSection('frontEditorEnable');

        $this->_sessionExpire = '+1 hour';
    }


    /**
     * Handle front editor enable.
     *
     * @param string $hash
     * @throws \Identity\IdentityException
     */
    public function handleFrontEditorEnable(string $hash)
    {
        $decode = $this->_identityModel->getDecodeHash($hash);

        $id = (int) $decode['id'];
        $item = $this->_identityModel->getById($id);  // load row from db
        if ($item && $id == $item['id']) { // not null result
            if ($this->_identityModel->verifyHash($item['id'] . $item['login'], $decode['verifyHash'])) {  // check hash and password
                $this->_section->setExpiration($decode['expired']);
                $this->_section->expired = $decode['expired'];
                $this->_section->enable = true;
            }
        }
        $this->redirect('this');
    }


    /**
     * Handle front editor disable.
     */
    public function handleFrontEditorDisable()
    {
        $this->_section->remove();
        $this->redirect('this');
    }


    /**
     * Is front editor enable.
     *
     * @return bool
     */
    public function isFrontEditorEnable(): bool
    {
        return $this->_section->enable ?? false;
    }


    /**
     * Get front editor enable link.
     *
     * @return string
     */
    public function getFrontEditorEnableLink(): string
    {
        $hash = $this->_identityModel->getEncodeHash($this->user->getId(), $this->user->getIdentity()->login, $this->_sessionExpire);
        return '?hash=' . $hash . '&do=FrontEditorEnable';
    }
}
