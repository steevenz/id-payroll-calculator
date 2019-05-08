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

namespace Steevenz\IndonesiaPayrollCalculator\PPh;

// ------------------------------------------------------------------------

/**
 * Class PPh21
 * @package Steevenz\IndonesiaPayrollCalculator
 */
class PPh21
{
    /**
     * PPh21::$listOfPTKP
     *
     * @var array
     */
    protected $listOfPTKP = [
        'TK/0' => 54000000,
        'K/0'  => 58500000,
        'K/1'  => 63000000,
        'K/2'  => 67500000,
        'K/3'  => 72000000,
    ];

    // ------------------------------------------------------------------------

    /**
     * PPh21::calculatePTKP
     *
     * @param bool $maritalStatus
     * @param int  $numOfDependentsFamily
     *
     * @return mixed
     */
    public function calculatePTKP($maritalStatus, $numOfDependentsFamily = 0)
    {
        if ($numOfDependentsFamily >= 3) {
            return $this->listOfPTKP[ 'K/3' ];
        } elseif ($numOfDependentsFamily == 2) {
            return $this->listOfPTKP[ 'K/2' ];
        } elseif ($numOfDependentsFamily == 1) {
            return $this->listOfPTKP[ 'K/1' ];
        } elseif($maritalStatus === false) {
            return $this->listOfPTKP[ 'TK/0' ];
        } else {
            return $this->listOfPTKP[ 'K/0' ];
        }
    }

    // ------------------------------------------------------------------------

    /**
     * PPh21::calculate
     *
     * @return float
     */
    protected function calculate()
    {
        $grossTotalIncome = $this->getGrossTotalIncome();

        $BPJSKesehatan = new BPJS\Kesehatan();
        $BPJSKesehatan->setEmployeeGrossTotalIncome($grossTotalIncome);
        $BPJSKesehatan->setNumOfDependentsFamily($this->numOfDependentsFamily);
        $BPJSKesehatan = $BPJSKesehatan->calculate();

        $BPJSKetenagakerjaan = new BPJS\Ketenagakerjaan();
        $BPJSKetenagakerjaan->setEmployeeGrossTotalIncome($grossTotalIncome);
        $BPJSKetenagakerjaan = $BPJSKetenagakerjaan->calculate();

        $this->allowances[ 'BPJS Ketenagakerjaan' ] = $BPJSKetenagakerjaan[ 'allowance' ];
        $this->allowances[ 'BPJS Kesehatan' ] = $BPJSKesehatan[ 'allowance' ];

        $this->deductions[ 'BPJS Ketenagakerjaan' ] = $BPJSKetenagakerjaan[ 'deduction' ];
        $this->deductions[ 'BPJS Kesehatan' ] = $BPJSKesehatan[ 'deduction' ];

        $this->deductions[ 'Position Deduction' ] = $this->getGrossTotalIncome() * (5 / 100);

        $monthlyNetIncome = $grossTotalIncome - array_sum($this->deductions);
        $yearlyNetIncome = $monthlyNetIncome * 12;

        if ($this->numOfDependentsFamily >= 3) {
            $PTKP = $this->listOfPTKP[ 'K/3' ];
        } elseif ($this->numOfDependentsFamily == 2) {
            $PTKP = $this->listOfPTKP[ 'K/2' ];
        } elseif ($this->numOfDependentsFamily == 1) {
            $PTKP = $this->listOfPTKP[ 'K/1' ];
        } else {
            $PTKP = $this->listOfPTKP[ 'TK/0' ];

            if ($this->married !== false) {
                $PTKP = $this->listOfPTKP[ 'K/0' ];
            }
        }

        $yearlyCalculation = $yearlyNetIncome - $PTKP;

        if ($this->married === 'JUST') {
            $yearlyCalculation = ($yearlyNetIncome - ($PTKP + 4500000)) * (5 / 100);
        }

        if ($this->NPWP) {
            return $yearlyCalculation / 12;
        }

        return ($yearlyCalculation / 12) * (120 / 100);
    }
}