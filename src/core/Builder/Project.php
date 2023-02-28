<?php


namespace Project\core\Builder;

use Project\Core\Builder\Project\ModulesGlobalization;
use Project\Core\Builder\Project\Simple;
use Project\Core\Builder\Project\Modules;
use Phalcon\Builder\BuilderException;
use Project\Core\Builder\Project\SimpleGlobalization;

/**
 * Class Project
 * @package Project\core\Builder
 */
class Project extends \Phalcon\Builder\Project
{
    use \Project\Traits\functionsTraits;

    const TYPE_SIMPLE  = 'simple';
    const TYPE_MODULES = 'modules';
    const TYPE_SIMPLE_GLOBALIZATION = 'simple_globalization';
    const TYPE_MODULES_GLOBALIZATION = 'modules_globalization';

    private $currentType = self::TYPE_SIMPLE;

    private $types = [
        self::TYPE_SIMPLE  => Simple::class,
        self::TYPE_MODULES => Modules::class,
        self::TYPE_SIMPLE_GLOBALIZATION => SimpleGlobalization::class,
        self::TYPE_MODULES_GLOBALIZATION => ModulesGlobalization::class,
    ];

    public function build()
    {
        if ($this->options->contains('directory')) {
            $path = realpath($this->options->get('directory')) ?: $this->options->get('directory');
            $this->path->setRootPath($path);
        } else {
            $this->path->setRootPath(getcwd());
        }

        $templatePath = TEMPLATE_PATH;

        if ($this->options->contains('templatePath')) {
            $templatePath = $this->options->get('templatePath');
        }

        if ($this->path->hasPhalconDir()) {
            throw new BuilderException('请在phalcon项目外创建新项目');
        }

        $this->currentType = $this->options->get('type');

        if (!isset($this->types[$this->currentType])) {
            throw new BuilderException(sprintf(
                '无效的项目结构："%s". 请从 [%s]中选择 ',
                $this->currentType,
                implode(', ', array_keys($this->types))
            ));
        }

        $builderClass = $this->types[$this->currentType];

        if ($this->options->contains('name')) {
            $this->path->appendRootPath($this->options->get('name'));
        }

        if (file_exists($this->path->getRootPath())) {
            throw new BuilderException(sprintf('目录 %s 已存在.', $this->path->getRootPath()));
        }

        if (!mkdir($this->path->getRootPath(), 0777, true)) {
            throw new BuilderException(sprintf('用户无权限创建目录 %s', $this->path->getRootPath()));
        }

        if (!is_writable($this->path->getRootPath())) {
            throw new BuilderException(sprintf('用户没有目录 %s 的写权限.', $this->path->getRootPath()));
        }

        $this->options->offsetSet('templatePath', $templatePath);
        $this->options->offsetSet('projectPath', $this->path->getRootPath());

        /** @var \Phalcon\Builder\Project\ProjectBuilder $builder */
        $builder = new $builderClass($this->options);

        $success = $builder->build();

        if ($success === true) {
            $sprintMessage = "项目 '%s' 创建成功.";
            $this->notifySuccess(sprintf($sprintMessage, $this->options->get('name')));
        }

        return $success;
    }
}