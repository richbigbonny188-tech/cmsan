<?php
/* --------------------------------------------------------------
   CronjobConfiguration.inc.php 2018-09-03
   Gambio GmbH
   http://www.gambio.de
   Copyright © 2018 Gambio GmbH
   --------------------------------------------------------------
*/

/**
 * Class CronjobConfiguration
 */
class CronjobConfiguration implements CronjobConfigurationInterface
{
    /**
     * @var string
     */
    protected $name;
    
    /**
     * @var string
     */
    protected $title;
    
    /**
     * @var string
     */
    protected $type;
    
    /**
     * @var mixed
     */
    protected $value;
    
    /**
     * @var array
     */
    protected $values;
    
    
    /**
     * CronjobConfiguration constructor.
     *
     * @param \StringType $name  Cronjob configuration name.
     * @param \StringType $title Cronjob configuration title.
     * @param \StringType $type  Cronjob configuration type.
     * @param mixed       $value Cronjob configuration value.
     */
    public function __construct(StringType $name, StringType $title, StringType $type, $value)
    {
        $this->name  = $name->asString();
        $this->title = $title->asString();
        $this->type  = $type->asString();
        $this->value = $value;
    }
    
    
    /**
     * Named constructor of cronjob configuration.
     *
     * @param string $name  Cronjob configuration name.
     * @param string $title Cronjob configuration title.
     * @param string $type  Cronjob configuration type.
     * @param mixed  $value Cronjob configuration value.
     *
     * @return \CronjobConfiguration New instance.
     */
    public static function create($name, $title, $type, $value)
    {
        $name  = new StringType($name);
        $title = new StringType($title);
        $type  = new StringType($type);
        
        return MainFactory::create(static::class, $name, $title, $type, $value);
    }
    
    
    /**
     * Named constructor of cronjob configuration.
     *
     * @param string $name   Cronjob configuration name.
     * @param string $title  Cronjob configuration title.
     * @param string $type   Cronjob configuration type.
     * @param mixed  $value  Cronjob configuration value.
     * @param array  $values Cronjob configuration values.
     *
     * @return \CronjobConfiguration New instance.
     */
    public static function withValues($name, $title, $type, $value, array $values)
    {
        $name  = new StringType($name);
        $title = new StringType($title);
        $type  = new StringType($type);
        
        /** @var \CronjobConfiguration $configuration */
        $configuration         = MainFactory::create(static::class, $name, $title, $type, $value);
        $configuration->values = $values;
        
        return $configuration;
    }
    
    
    /**
     * Returns the cronjob configurations name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    
    /**
     * * Returns the cronjob configurations title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
    
    
    /**
     * * Returns the cronjob configurations type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
    
    
    /**
     * Returns the cronjob configuration field.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
    
    
    /**
     * Returns the configuration values. Used by selects.
     *
     * @return array|null
     */
    public function getValues()
    {
        return $this->values;
    }
    
    
    /**
     * Returns the cronjob configuration data as array.
     *
     * @return array
     */
    public function toArray()
    {
        $data = [
            'name'  => $this->getName(),
            'title' => $this->getTitle(),
            'type'  => $this->getType(),
            'value' => $this->getValue()
        ];
        if ($this->getValues()) {
            $data['values'] = $this->getValues();
        }
        
        return $data;
    }
}