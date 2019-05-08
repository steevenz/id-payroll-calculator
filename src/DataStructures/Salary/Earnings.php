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

namespace Steevenz\IndonesiaPayrollCalculator\DataStructures\Salary;

// ------------------------------------------------------------------------

/**
 * Class Earnings
 * @package Steevenz\IndonesiaPayrollCalculator\DataStructures\Salary
 */
class Earnings
{
    /**
     * Earnings::$basic
     *
     * @var float
     */
    public $basic = 0;

    /**
     * Earnings::$overtime
     *
     * @var float
     */
    public $overtime = 0;

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


}