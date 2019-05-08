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

namespace Steevenz\IndonesiaPayrollCalculator\Calculators;

// ------------------------------------------------------------------------

use Steevenz\IndonesiaPayrollCalculator\Calculators\Abstracts\AbstractCalculator;
use Steevenz\IndonesiaPayrollCalculator\DataStructures\Presences;
use Steevenz\IndonesiaPayrollCalculator\DataStructures\Salary\Allowances;
use Steevenz\IndonesiaPayrollCalculator\PPh\PPh21;

/**
 * Class Earnings
 * @package Steevenz\IndonesiaPayrollCalculator\Calculators
 */
class Earnings extends AbstractCalculator
{
    /**
     * Earnings::calculateBasicSalary
     *
     * @return bool|float Returns FALSE if failed.
     */
    public function calculateBasicSalary()
    {
        if (($presences = $this->employee->getPresences()) instanceof Presences and ($earnings = $this->employee->getEarnings()) instanceof \Steevenz\IndonesiaPayrollCalculator\DataStructures\Salary\Earnings) {
            return $presences->getCalculatedDays() * ($earnings->basic / $presences->calculatedWorkingDays);
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Earnings::calculateOvertime
     *
     * @return bool|float Returns FALSE if failed.
     */
    public function calculateOvertime()
    {
        if (($presences = $this->employee->getPresences()) instanceof Presences and ($earnings = $this->employee->getEarnings()) instanceof \Steevenz\IndonesiaPayrollCalculator\DataStructures\Salary\Earnings) {
            return $presences->overtime * $earnings->overtime;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Earnings::calculateGrossTotalIncome
     *
     * @return bool|float Returns FALSE if failed.
     */
    public function calculateGrossTotalIncome()
    {
        if(false !== ($basicSalary = $this->calculateBasicSalary()) and false !==($overtime = $this->calculateOvertime())) {
            $grossTotalIncome = $basicSalary + $overtime;

            if(($allowances = $this->employee->getAllowances()) instanceof Allowances) {
                if($allowances->count()) {
                    $allowances = $allowances->getArrayCopy();
                    $grossTotalIncome = $grossTotalIncome + array_sum($allowances);
                }
            }

            return $grossTotalIncome;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    public function calculatePph21()
    {
        $pph21 = new PPh21($this->employee);
    }
}