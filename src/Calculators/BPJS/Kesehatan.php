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

namespace Steevenz\IndonesiaPayrollCalculator\BPJS;

// ------------------------------------------------------------------------

use Steevenz\IndonesiaPayrollCalculator\BPJS\Abstracts\AbstractBPJS;

/**
 * Class Kesehatan
 * @package Steevenz\IndonesiaPayrollCalculator\BPJS
 */
class Kesehatan extends AbstractBPJS
{
    /**
     * Kesehatan::getSubscriptionClass
     *
     * Gets BPJS Kesehatan grade class based on Gross Total Income.
     *
     * @return int
     */
    public function getSubscriptionClass($grossTotalIncome)
    {
        if ($grossTotalIncome <= 4000000) {
            return 2;
        } elseif ($grossTotalIncome >= 8000000) {
            return 1;
        }

        return 3;
    }

    // ------------------------------------------------------------------------

    /**
     * Kesehatan::calculate
     * 
     * @param int $numOfDependentsFamily
     *
     * @return array
     */
    public function calculate($grossTotalIncome, $numOfDependentsFamily = 0)
    {
        // $grossTotalIncome < $this->highestMonthlySalary
        $allowance = $grossTotalIncome * (4 / 100);
        $deduction = $grossTotalIncome * (1 / 100);

        if ($grossTotalIncome >= $this->ump && $grossTotalIncome >= $this->highestMonthlySalary) {
            $allowance = $this->highestMonthlySalary * (4 / 100);
            $deduction = $this->highestMonthlySalary * (1 / 100);
        }

        if ($numOfDependentsFamily > 5) {
            $deduction = $deduction + ($deduction * ($numOfDependentsFamily - 5));
        }

        return [
            'allowance' => $allowance,
            'deduction' => $deduction,
        ];
    }
}