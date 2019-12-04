<?php
/**
 * This file is part of the Payroll Calculator Package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         Steeve Andrian Salim
 * @copyright      Copyright (c) Steeve Andrian Salim
 */
// ------------------------------------------------------------------------

namespace Steevenz\IndonesiaPayrollCalculator\DataStructures\Employee;

// ------------------------------------------------------------------------

/**
 * Class Presences
 * @package Steevenz\IndonesiaPayrollCalculator\DataStructures
 */
class Presences
{
    /**
     * Presences::$workDays
     *
     * @var int
     */
    public $workDays = 0;

    /**
     * Presences::$overtime
     *
     * @var int
     */
    public $overtime = 0;

    /**
     * Presences::$splitShifts
     * 
     * @var int 
     */
    public $splitShifts = 0;

    /**
     * Presences::$latetime
     *
     * @var int
     */
    public $latetime = 0;

    /**
     * Presences::$travelDays
     *
     * @var int
     */
    public $travelDays = 0;

    /**
     * Presences::$leaveDays
     *
     * @var int
     */
    public $leaveDays = 0;

    /**
     * Presences::$indisposeDays
     *
     * @var int
     */
    public $indisposeDays = 0;

    /**
     * Presences::$absentDays
     *
     * @var int
     */
    public $absentDays = 0;

    // ------------------------------------------------------------------------

    /**
     * Presences::__set
     *
     * @param string $name
     * @param int    $value
     */
    public function __set($name, $value)
    {
        if(is_int($value)) {
            $this->{$name} = $value;
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Presences::__get
     *
     * @param string $name
     *
     * @return int
     */
    public function __get($name)
    {
        if(property_exists($this, $name)) {
            return (int) $this->{$name};
        }

        return 0;
    }

    // ------------------------------------------------------------------------

    /**
     * Presences::getCalculatedDays
     *
     * @return int
     */
    public function getCalculatedDays()
    {
        return $this->workDays + $this->leaveDays + $this->indisposeDays + $this->travelDays;
    }
}