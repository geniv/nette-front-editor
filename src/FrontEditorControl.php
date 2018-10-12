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
    private $identityModel;
    /** @var SessionSection */
    private $section;


    /**
     * FrontEditorControl constructor.
     *
     * @param IIdentityModel $identityModel
     * @param Session        $session
     */
    public function __construct(IIdentityModel $identityModel, Session $session)
    {
        $this->identityModel = $identityModel;
        $this->section = $session->getSection('frontEditorEnable');
    }


    /**
     * Handle front editor enable.
     *
     * @param string $hash
     * @throws \Identity\IdentityException
     */
    public function handleFrontEditorEnable(string $hash)
    {
        $decode = $this->identityModel->getDecodeHash($hash);

        $id = (int) $decode['id'];
        $item = $this->identityModel->getById($id);  // load row from db
        if ($item && $id == $item['id']) { // not null result
            if ($this->identityModel->verifyHash($item['id'] . $item['login'], $decode['verifyHash'])) {  // check hash and password
                $this->section->setExpiration($decode['expired']);
                $this->section->expired = $decode['expired'];
                $this->section->enable = true;
            }
        }
    }


    /**
     * Handle front editor disable.
     */
    public function handleFrontEditorDisable()
    {
        $this->section->remove();
    }


    /**
     * Is front editor enable.
     *
     * @return bool
     */
    public function isFrontEditorEnable(): bool
    {
        return $this->section->enable ?? false;
    }
}
