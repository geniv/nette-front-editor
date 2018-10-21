<?php declare(strict_types=1);

namespace FrontEditor\Bridges\Nette;

use FrontEditor\FormContainer;
use FrontEditor\FrontEditor;
use GeneralForm\GeneralForm;
use Nette\DI\CompilerExtension;


/**
 * Class Extension
 *
 * @author  geniv
 * @package FrontEditor\Bridges\Nette
 */
class Extension extends CompilerExtension
{
    /** @var array default values */
    private $defaults = [
        'autowired'     => true,
        'formContainer' => FormContainer::class,
        'adminLink'     => null,
    ];


    /**
     * Load configuration.
     */
    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();
        $config = $this->validateConfig($this->defaults);

        $formContainer = GeneralForm::getDefinitionFormContainer($this);

        // define form
        $form = $builder->addDefinition($this->prefix('form'))
            ->setFactory(FrontEditor::class, [$formContainer])
            ->setAutowired($config['autowired']);

        if (isset($config['adminLink']) && $config['adminLink']) {
            $form->setAdminLink($config['adminLink']);
        }
    }
}
