<?php

namespace Framework\Commands\Create;

class Create
{
    use \Framework\Commands\DefaultTraits;
    use \Framework\Commands\Create\TemplateTraits;

    /**
     * Contains all generate types from json
     *
     * @var array
     */
    public $generateTypes = [];

    /**
     * Contains selected generate type
     *
     * @var null
     */
    public $generateType = null;

    /**
     * Contains names of generate types
     *
     * @var array
     */
    public $generateTypeNames = [];

    /**
     * start function
     *
     * @return void
     */
    public function start() : void
    {
        $this->generateTypes = json_decode(file_get_contents(__DIR__ . '/GenerateTypes.json'), true);
        $this->generateTypeNames();
        $this->requestType();
    }

    /**
     * Generate type names from multi-d arrays
     *
     * @return void
     */
    public function generateTypeNames() : void
    {
        foreach ($this->generateTypes as $key => $generateType) {
            $this->generateTypeNames[] = $key;
        }
    }

    /**
     * Request type user wants to create
     *
     * @return void
     */
    public function requestType() : void
    {
        $generateType = null;
        while (!in_array($generateType, $this->generateTypeNames)) {
            $generateType = self::requestInput('What type of file do you want to generate?', $this->generateTypeNames);
            if (!in_array($generateType, $this->generateTypeNames)) {
                self::reportError('This option is not valid');
            }
        }
        $this->generateType= $generateType;
        $this->requestName();
    }

    /**
     * Request the name the user wants to give the item they are generating
     *
     * @return void
     */
    public function requestName() : void
    {
        $name = self::requestInput('What do you want the name to be:');

        if ($name[0] == '/') {
            $name = substr($name, 1);
        }

        if (strpos($name, '/')) {
            $pathToCreate = '';
            $pathArray = explode('/', $name);
            foreach ($pathArray as $key => $path) {
                if ($key < count($pathArray)-1) {
                    $pathToCreate .= '/'.$path;
                }
            }
            mkdir(__DIR__ . '/../../../'.$this->generateTypes[$this->generateType]['path'] . '/' . $pathToCreate, 0777, true);
            $path = $this->generateTypes[$this->generateType]['path'] . $pathToCreate;
        }
        else {
            $path = $this->generateTypes[$this->generateType]['path'];
        }

        $namespace = str_replace('/', '\\', $path);

        $name = (strpos($name, '/') ? end($pathArray) : $name ) . $this->getNameExtension($this->generateType);

        $file = __DIR__ . '/Templates/'.$this->generateType.'Template.php';
        include_once $file;

        $newFile = fopen(__DIR__ . '/../../../'.$path.'/' . $name . $this->generateTypes[$this->generateType]['extension'], 'w');
        fwrite($newFile, $fileContent);

        echo 'Your ' . $this->generateType . ' has been generated';
    }
}