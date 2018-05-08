<?php declare(strict_types=1);

namespace FrontEditor;

use GeneralForm\IFormContainer;
use GeneralForm\ITemplatePath;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;


/**
 * Class FrontEditor
 *
 * @author  geniv
 * @package FrontEditor
 */
class FrontEditor extends Control implements ITemplatePath
{
    /** @var IFormContainer */
    private $formContainer;
    /** @var ITranslator */
    private $translator;
    /** @var string */
    private $templatePath;
    /** @var bool */
    private $acl = false;
    /** @var bool */
    private $editor = false;
    /** @var string */
    private $data;
    /** @var callable */
    public $onSuccess;
    /** @var array */
    private $variableTemplate = [];


    /**
     * FrontEditor constructor.
     *
     * @param IFormContainer   $formContainer
     * @param ITranslator|null $translator
     */
    public function __construct(IFormContainer $formContainer, ITranslator $translator = null)
    {
        parent::__construct();

        $this->formContainer = $formContainer;
        $this->translator = $translator;

        $this->templatePath = __DIR__ . '/FrontEditor.latte'; // set path
    }


    /**
     * Set template path.
     *
     * @param string $path
     */
    public function setTemplatePath(string $path)
    {
        $this->templatePath = $path;
    }


    /**
     * Add variable template.
     *
     * @param string $name
     * @param        $values
     */
    public function addVariableTemplate(string $name, $values)
    {
        $this->variableTemplate[$name] = $values;
    }


    /**
     * Render.
     */
    public function render()
    {
        $template = $this->getTemplate();

        // add user defined variable
        foreach ($this->variableTemplate as $name => $value) {
            $template->$name = $value;
        }

        $template->acl = $this->acl;
        $template->editor = $this->editor;
        $template->data = $this->data;

        $template->setTranslator($this->translator);
        $template->setFile($this->templatePath);
        $template->render();
    }


    /**
     * Set ACL.
     *
     * @param bool $acl
     */
    public function setAcl(bool $acl)
    {
        $this->acl = $acl;
    }


    /**
     * Set data.
     *
     * @param string $data
     */
    public function setData(string $data)
    {
        $this->data = $data;
    }


    /**
     * Handle switch editor.
     *
     * @param bool $state
     */
    public function handleSwitchEditor(bool $state)
    {
        $this->editor = $state;

        if ($this->presenter->isAjax()) {
            $this->redrawControl('content');
        }
    }


    /**
     * Create component form.
     *
     * @param string $name
     * @return Form
     */
    protected function createComponentForm(string $name): Form
    {
        $form = new Form($this, $name);
        $form->setTranslator($this->translator);
        $this->formContainer->getForm($form);

        // load data to form
        $form->setDefaults(['content' => $this->data]);

        $form->onSuccess[] = function (Form $form, array $values) {
            $this->onSuccess($form, $values);
        };
        return $form;
    }
}
