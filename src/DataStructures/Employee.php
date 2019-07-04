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

namespace Steevenz\IndonesiaPayrollCalculator\DataStructures;

// ------------------------------------------------------------------------

/**
 * Class Employee
 * @package Steevenz\IndonesiaPayrollCalculator\DataStructures
 */
class Employee
{
    /**
     * Employee::$maritalStatus
     *
     * @var bool
     */
    public $maritalStatus = false;

    /**
     * Employee::$hasNPWP
     *
     * @var bool
     */
    public $hasNPWP = true;

    /**
     * Employee::$numOfDependentsFamily
     *
     * @var int
     */
    public $numOfDependentsFamily = 0;

    /**
     * Employee::$presences
     *
     * @var \Steevenz\IndonesiaPayrollCalculator\DataStructures\Employee\Presences
     */
    public $presences;

    /**
     * Employee::$earnings
     *
     * @var \Steevenz\IndonesiaPayrollCalculator\DataStructures\Employee\Earnings
     */
    public $earnings;

    /**
     * Employee::$allowances
     *
     * @var \Steevenz\IndonesiaPayrollCalculator\DataStructures\Employee\Allowances
     */
    public $allowances;

    /**
     * Employee::$deductions
     *
     * @var \Steevenz\IndonesiaPayrollCalculator\DataStructures\Employee\Deductions
     */
    public $deductions;

    /**
     * Company::$calculateHolidayAllowance
     *
     * @var int
     */
    public $calculateHolidayAllowance = 0;

    /**
     * Employee::$bonus
     *
     * @var \Steevenz\IndonesiaPayrollCalculator\DataStructures\Employee\Bonus
     */
    public $bonus;

    // ------------------------------------------------------------------------

    /**
     * Employee::__construct
     */
    public function __construct()
    {
        $this->presences = new Employee\Presences();
        $this->earnings = new Employee\Earnings();
        $this->allowances = new Employee\Allowances();
        $this->deductions = new Employee\Deductions();
        $this->bonus = new Employee\Bonus();
    }
}