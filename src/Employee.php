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

namespace Steevenz\IndonesiaPayrollCalculator;

// ------------------------------------------------------------------------
use Steevenz\IndonesiaPayrollCalculator\Calculators\Earnings;
use Steevenz\IndonesiaPayrollCalculator\PPh\PPh21;
use O2System\Spl\DataStructures\SplArrayObject;

/**
 * Class Employee
 * @package Steevenz\IndonesiaPayrollCalculator
 */
class Employee
{
    /**
     * Employee::$maritalStatus
     *
     * Employee marital status, fill with TRUE, FALSE or JUST.
     *
     * @var bool|string
     */
    protected $maritalStatus = false;

    /**
     * Employee::$npwp
     *
     * Employee Nomor Pengguna Wajib Pajak (NPWP).
     *
     * @var bool|string
     */
    protected $hasNPWP = true;

    /**
     * Employee::$numOfDependentsFamily
     *
     * @var int
     */
    protected $numOfDependentsFamily = 0;

    /**
     * Employee::$presences
     *
     * @var \Steevenz\IndonesiaPayrollCalculator\DataStructures\Presences
     */
    protected $presences;

    /**
     * Employee::$earnings
     *
     * @var \Steevenz\IndonesiaPayrollCalculator\DataStructures\Salary\Earnings
     */
    public $earnings;

    /**
     * Employee::$allowances
     *
     * @var \Steevenz\IndonesiaPayrollCalculator\DataStructures\Salary\Allowances
     */
    public $allowances;

    /**
     * Employee::$deductions
     *
     * @var \Steevenz\IndonesiaPayrollCalculator\DataStructures\Salary\Deductions
     */
    public $deducations;

    // ------------------------------------------------------------------------

    /**
     * Employee::setMarried
     *
     * @param bool $maritalStatus
     *
     * @return static
     */
    public function setMaritalStatus($maritalStatus)
    {
        if (in_array($maritalStatus, [true, false, 'JUST'])) {
            $this->maritalStatus = $maritalStatus;
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Employee::getMaritalStatus
     *
     * @return bool|string
     */
    public function getMaritalStatus()
    {
        return $this->maritalStatus;
    }

    // ------------------------------------------------------------------------

    /**
     * Employee::setNpwp
     *
     * @param bool $hasNPWP
     *
     * @return static
     */
    public function setHasNPWP($hasNPWP)
    {
        $this->hasNPWP = (bool)$hasNPWP;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Employee::getNpwp
     *
     * @return bool|string
     */
    public function getHasNPWP()
    {
        return $this->hasNPWP;
    }

    // ------------------------------------------------------------------------

    /**
     * Employee::setNumOfDependentsFamily
     *
     * @param int $numOfDependentsFamily
     *
     * @return static
     */
    public function setNumOfDependentsFamily($numOfDependentsFamily)
    {
        $this->numOfDependentsFamily = (int)$numOfDependentsFamily;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Employee::getNumOfDependentsFamily
     *
     * @return int
     */
    public function getNumOfDependentsFamily()
    {
        return $this->numOfDependentsFamily;
    }

    // ------------------------------------------------------------------------

    /**
     * Employee::setPresences
     *
     * @param \Steevenz\IndonesiaPayrollCalculator\DataStructures\Presences $presences
     *
     * @return static
     */
    public function setPresences(DataStructures\Presences $presences)
    {
        $this->presences = $presences;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Employee::getPresences
     *
     * @return \Steevenz\IndonesiaPayrollCalculator\DataStructures\Presences
     */
    public function getPresences()
    {
        return $this->presences;
    }

    // ------------------------------------------------------------------------

    /**
     * Employee::setEarnings
     *
     * @param \Steevenz\IndonesiaPayrollCalculator\DataStructures\Salary\Earnings $earnings
     *
     * @return static
     */
    public function setEarnings(DataStructures\Salary\Earnings $earnings)
    {
        $this->earnings = $earnings;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Employee::getEarnings
     *
     * @return \Steevenz\IndonesiaPayrollCalculator\DataStructures\Salary\Earnings
     */
    public function getEarnings()
    {
        return $this->earnings;
    }

    // ------------------------------------------------------------------------


    /**
     * Employee::setAllowances
     *
     * @param \Steevenz\IndonesiaPayrollCalculator\DataStructures\Salary\Allowances $allowances
     *
     * @return static
     */
    public function setAllowances(DataStructures\Salary\Allowances $allowances)
    {
        $this->allowances = $allowances;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Employee::getAllowances
     *
     * @return \Steevenz\IndonesiaPayrollCalculator\DataStructures\Salary\Allowances
     */
    public function getAllowances()
    {
        return $this->allowances;
    }

    // ------------------------------------------------------------------------

    /**
     * Employee::setDeductions
     *
     * @param \Steevenz\IndonesiaPayrollCalculator\DataStructures\Salary\Deductions $deductions
     *
     * @return static
     */
    public function setDeductions(DataStructures\Salary\Deductions $deductions)
    {
        $this->deducations = $deductions;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Employee::getDeductions
     *
     * @return \Steevenz\IndonesiaPayrollCalculator\DataStructures\Salary\Deductions
     */
    public function getDeductions()
    {
        return $this->deducations;
    }

    // ------------------------------------------------------------------------

    /**
     * Employee::getPayroll
     */
    public function getPayroll()
    {
        $payroll = new SplArrayObject();

        $earnings = new Earnings($this);

        $payroll->offsetSet('earnings', [
            'basicSalary' => $earnings->calculateBasicSalary(),
            'overtime' => $earnings->calculateOvertime(),
        ]);
    }
}