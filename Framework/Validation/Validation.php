<?php

namespace Framework\Validation;

use Framework\Database\Database;
use Framework\Validation\ValidationMethods;

class Validation
{
    use ValidationMethods;

    /**
     * Contains all the posted data from the form
     * 
     * @var array
     */
    public $postData = [];

    /**
     * Contains all the filter data
     * 
     * @var array
     */
    public $filterData = [];

    /**
     * Contains all the validation errors
     * 
     * @var array
     */
    public $validationErrors = [];

    /**
     * Contains all the custom messages that are set for validation methods
     * 
     * @var array
     */
    public $customMessages = [];

    /**
     * Contains database connection
     * 
     * @var array
     */
    private $database;

    /**
     * Var to check if filters have to continue or stop because required is not met for example
     * 
     * @var boolean
     */
    private $runFilters = true;

    /**
     * Construct method
     * 
     * @param array $postData
     * @param array $filterData
     * @param array @customMessages
     * @return void
     */
    public function __construct(array $postData, array $filterData, array $customMessages = null, string $databaseConnectionName = null)
    {
        $this->postData = $postData;
        $this->filterData = $filterData;
        if ($customMessages !== null) {
            $this->customMessages = $customMessages;
        }

        if ($databaseConnectionName === null) {
            $this->database = reset(Database::$Connections);
        }
        else {
            $this->database = Database::$Connections[$databaseConnectionName];
        }

        $this->handleFilters();
    }

    /**
     * Loops trough all filters for each form input
     * 
     * @return void
     */
    private function handleFilters()
    {
        foreach ($this->postData as $key => $data) {
            if (isset($this->filterData[$key])) {
                $this->runFilters = true;

                foreach ($this->filterData[$key] as $filterType) {
                    if ($this->runFilters === true) {
                        $this->runCorrectFilter([$key, $data], $filterType);
                    }
                }
            }
        }
    }

    /**
     * Check witch filter needs to be checked for input
     * 
     * @param array $input
     * @param string $filterType
     */
    private function runCorrectFilter(array $input, string $filterType)
    {
        $input[] = $filterType;
        if (strpos($filterType, ':') !== false) {
            list($filterType, $filterParameter) = explode(':', $filterType);
        }
        switch ($filterType) {
            case 'required':
                $input[] = "$input[0] field is required.";
                $this->requiredFilter($input);
                break;
            case 'email':
                $input[] = "$input[0] field is not a valid email.";
                $this->emailFilter($input);
                break;
            case 'string':
                $input[] = "$input[0] field is not of type string.";
                $this->stringFilter($input);
                break;
            case 'integer':
                $input[] = "$input[0] field is not of type integer.";
                $this->intFilter($input);
                break;
            case 'confirmation':
                $input[] = "$input[0] confirmation field is not the same.";
                $this->confirmationFilter($input);
                break;
            case 'boolean':
                $input[] = "$input[0] field is not of type boolean.";
                $this->booleanFilter($input, $filterParameter);
                break;
            case 'array':
                $input[] = "$input[0] field is not of type array.";
                $this->arrayFilter($input);
                break;
            case 'active_url':
                $input[] = "$input[0] field is not an active url.";
                $this->activeUrlFilter($input);
                break;
            case 'after':
                $input[] = "$input[0] field is not after date $filterParameter.";
                $this->afterDateFilter($input, $filterParameter);
                break;
            case 'date':
                $input[] = "$input[0] field is not of type date.";
                $this->dateFilter($input);
                break;
            case 'different':
                $input[] = "$input[0] field is same as $filterParameter.";
                $this->differentFilter($input, $filterParameter);
                break;
            case 'ipaddress':
                $input[] = "$input[0] field is not of type ipaddress.";
                $this->ipaddressFilter($input);
                break;
            case 'json':
                $input[] = "$input[0] field is not of type JSON.";
                $this->jsonCheck($input);
                break;
            case 'length':
                $input[] = "$input[0] field is not the same length as $filterParameter.";
                $this->lengthCheck($input, $filterParameter);
                break;
            case 'unique':
                $input[] = "$input[0] field is not unique.";
                $this->uniqueFilter($input, $filterParameter);
                break;
            case 'phone':
                $input[] = "$input[0] field is not a phone number.";
                $this->phoneFilter($input);
                break;
            case 'exists':
                $input[] = "$input[0] field does not exist.";
                $this->existsFilter($input, $filterParameter);
                break;
            case 'image':
                $input[] = "$input[0] is not of type image.";
                $this->imageFilter($input);
                break;
            case 'size':
                $input[] = "$input[0] is too large";
                $this->fileSizeFilter($input, $filterParameter);
                break;
            case 'file':
                $input[] = "$input[0] is not an existing file";
                $this->fileFilter($input);
                break;
        }
    }

    /**
     * Handle new validation error
     * 
     * @param array $input
     * @return void
     */
    private function newValidationError(array $input)
    {
        if (count($this->customMessages) !== 0) {
            $customMessageKey = $input[0] . '.' . $input[2];
            if (isset($this->customMessages[$customMessageKey])) {
                $input[3] = $this->customMessages[$customMessageKey];
            }
        }

        $this->validationErrors[] = [
            'name' => $input[0],
            'value' => $input[1],
            'message' => $input[3]
        ];
    }
}
