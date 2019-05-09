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

use O2System\Spl\DataStructures\SplArrayObject;
use Steevenz\IndonesiaPayrollCalculator\DataStructures;

/**
 * Class PayrollCalculator
 * @package Steevenz\IndonesiaPayrollCalculator
 */
class PayrollCalculator
{
    /**
     * PayrollCalculator::$provisions
     *
     * @var \Steevenz\IndonesiaPayrollCalculator\DataStructures\Provisions
     */
    public $provisions;

    /**
     * PayrollCalculator::$employee
     *
     * @var \Steevenz\IndonesiaPayrollCalculator\DataStructures\Employee
     */
    public $employee;

    // ------------------------------------------------------------------------

    /**
     * PayrollCalculator::__construct
     *
     * @param array $data
     */
    public function __construct()
    {
        $this->provisions = new DataStructures\Provisions();
        $this->employee = new DataStructures\Employee();
    }

    // ------------------------------------------------------------------------

    /**
     * PayrollCalculator::getDailyBasePay
     */
    public function getDailyBasePay()
    {
        return $this->employee->earnings->basePay / $this->provisions->company->numOfWorkingDays;
    }

    // ------------------------------------------------------------------------

    /**
     * PayrollCalculator::getCalculation
     */
    public function getCalculation()
    {
        // Calculate Basic Salary
        $basePay = $this->getDailyBasePay() * $this->employee->presences->getCalculatedDays();
        $absentPenalty = $this->employee->presences->absentDays * $this->provisions->company->absentPenalty;
        $latetimePenalty = $this->employee->presences->latetime * $this->provisions->company->latetimePenalty;

        $basePay = $basePay - $absentPenalty - $latetimePenalty;
        $overtime = $this->employee->earnings->overtimePay * $this->employee->presences->overtime;

        // Calculate Gross Total Income
        $grossTotalIncome = $basePay + $overtime;

        if ($this->provisions->company->calculateBPJSKesehatan === true) {
            // Calculate BPJS Kesehatan Allowance & Deduction
            if ($this->employee->allowances->count()) {
                $this->employee->allowances->BPJSKesehatan = ($grossTotalIncome + array_sum($this->employee->allowances->getArrayCopy())) * (4 / 100);
                $this->employee->deductions->BPJSKesehatan = ($grossTotalIncome + array_sum($this->employee->allowances->getArrayCopy())) * (1 / 100);
            } else {
                $this->employee->allowances->BPJSKesehatan = $grossTotalIncome * (4 / 100);
                $this->employee->deductions->BPJSKesehatan = $grossTotalIncome * (1 / 100);
            }

            // Maximum number of dependents family is 5
            if ($this->employee->numOfDependentsFamily > 5) {
                $this->employee->deductions->BPJSKesehatan = $this->employee->deductions->BPJSKesehatan + ($this->employee->deductions->BPJSKesehatan * ($this->employee->numOfDependentsFamily - 5));
            }
        }

        // Calculate BPJS Ketenagakerjaan
        if($this->provisions->company->calculateBPJSKetenagakerjaan === true) {
            if ($grossTotalIncome < $this->provisions->state->highestWage) {

                $this->employee->allowances->JKK = $grossTotalIncome * $this->provisions->state->getJKKRiskGradePercentage($this->provisions->company->riskGrade);

                /**
                 * Perhitungan JKM
                 *
                 * Iuran jaminan kematian (JKM) sebesar 0,30% dari upah sebulan.
                 * Ditanggung sepenuhnya oleh perusahaan.
                 */
                $this->employee->allowances->JKM = $grossTotalIncome * (0.30 / 100);

                /**
                 * Perhitungan JHT
                 *
                 * Iuran jaminan hari tua (JHT) sebesar 5,7% dari upah sebulan,
                 * dengan ketentuan 3,7% ditanggung oleh pemberi kerja dan 2% ditanggung oleh pekerja.
                 */
                $this->employee->allowances->JHT = $grossTotalIncome * (3.7 / 100);
                $this->employee->deductions->JHT = $grossTotalIncome * (2 / 100);

                /**
                 * Perhitungan JP
                 *
                 * Iuran jaminan pensiun (JP) sebesar 3% dari upah sebulan,
                 * dengan ketentuan 2% ditanggung oleh pemberi kerja dan 1% ditanggung oleh pekerja.
                 */
                $this->employee->allowances->JIP = $grossTotalIncome * (2 / 100);
                $this->employee->deductions->JIP = $grossTotalIncome * (1 / 100);

            } elseif ($grossTotalIncome >= $this->provisions->state->provinceMinimumWage && $grossTotalIncome >= $this->provisions->state->highestWage) {
                $this->employee->allowances->JKK = $this->provisions->state->highestWage * $this->provisions->state->getJKKRiskGradePercentage($this->provisions->company->riskGrade);


                /**
                 * Perhitungan JKM
                 *
                 * Iuran jaminan kematian (JKM) sebesar 0,30% dari upah sebulan.
                 * Ditanggung sepenuhnya oleh perusahaan.
                 */
                $this->employee->allowances->JKM = $this->provisions->state->highestWage * (0.30 / 100);

                /**
                 * Perhitungan JHT
                 *
                 * Iuran jaminan hari tua (JHT) sebesar 5,7% dari upah sebulan,
                 * dengan ketentuan 3,7% ditanggung oleh pemberi kerja dan 2% ditanggung oleh pekerja.
                 *
                 * Dihitung dari batas nilai upah bulanan tertinggi, karena total income melebihi UMP.
                 */
                $this->employee->allowances->JHT = $this->provisions->state->highestWage * (3.7 / 100);
                $this->employee->deductions->JHT = $this->provisions->state->highestWage * (2 / 100);

                /**
                 * Perhitungan JP
                 *
                 * Iuran jaminan pensiun (JP) sebesar 3% dari upah sebulan,
                 * dengan ketentuan 2% ditanggung oleh pemberi kerja dan 1% ditanggung oleh pekerja.
                 *
                 * Maximum Perhitungan total income jaminan pensiun adalah 7.000.000
                 */
                $this->employee->allowances->JIP = 7000000 * (2 / 100);
                $this->employee->deductions->JIP = 7000000 * (1 / 100);
            }
        }

        // Re-calculate gross total income
        $grossTotalIncome = $grossTotalIncome + array_sum($this->employee->allowances->getArrayCopy());

        $this->employee->deductions->positions = 0;
        if ($grossTotalIncome > $this->provisions->state->provinceMinimumWage) {

            /**
             * According to Undang-Undang Direktur Jenderal Pajak Nomor PER-32/PJ/2015 Pasal 21 ayat 3
             * Position Deduction is 5% from Yearly Gross Income
             */
            $this->employee->deductions->positions = $grossTotalIncome * (5 / 100);

            /**
             * Maximum Position Deduction in Indonesia is 500000 / month
             * or 6000000 / year
             */
            if ($this->employee->deductions->positions >= 500000) {
                $this->employee->deductions->positions = 500000;
            }
        }

        // Calculate Monthly Net Income
        if ($this->employee->deductions->count()) {
            $monthlyNetIncome = $grossTotalIncome - array_sum($this->employee->deductions->getArrayCopy());
        } else {
            $monthlyNetIncome = $grossTotalIncome;
        }

        $PTKP = 0;
        $PKP = 0;

        /**
         * PPh21 dikenakan bagi yang memiliki penghasilan lebih dari 4500000
         */
        if($monthlyNetIncome > 4500000) {
            // Calculate Yearly Net Income
            $yearlyNetIncome = $monthlyNetIncome * 12;

            // Yearly PTKP base on number of dependents family
            $PTKP = $this->provisions->state->getPTKP($this->employee->numOfDependentsFamily);
            $yearlyPKP = $yearlyNetIncome - $PTKP;

            if ($this->employee->maritalStatus === true) {
                $yearlyPKP = $yearlyPKP - $this->provisions->state->additionalPTKPforMarriedEmployees;
            }

            //$PKP = ($yearlyNetIncome - $PTKP) * $this->provisions->state->getPPh21Rate($monthlyNetIncome);
        }

        $yearlyPPh21 = $yearlyPKP * $this->provisions->state->getPPh21Rate($monthlyNetIncome);

        // Jika tidak memiliki NPWP dikenakan tambahan 20%
        if($this->employee->hasNPWP === false) {
            $yearlyPPh21 = $yearlyPKP * ($this->provisions->state->getPPh21Rate($monthlyNetIncome) + (20 /100));
        }

        $monthlyPPh21 = $yearlyPPh21 / 12;

        return new SplArrayObject([
            'earnings' => new SplArrayObject([
                'basePay' => $basePay,
                'overtime' => $overtime,
            ]),
            'allowances' => $this->employee->allowances,
            'deductions' => $this->employee->deductions,
            'ownPTKP' => $PTKP,
            'maritalPTKP' => $this->employee->maritalStatus ? $this->provisions->state->additionalPTKPforMarriedEmployees : 0,
            'yearlyPKP' => $yearlyPKP,
            'yearlyPPh21' => $yearlyPPh21,
            'monthlyPPh21' => $monthlyPPh21
        ]);
    }
}