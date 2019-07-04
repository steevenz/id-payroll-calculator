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
use Steevenz\IndonesiaPayrollCalculator\Taxes\Pph21;
use Steevenz\IndonesiaPayrollCalculator\Taxes\Pph23;

/**
 * Class PayrollCalculator
 * @package Steevenz\IndonesiaPayrollCalculator
 */
class PayrollCalculator
{
    /**
     * PayrollCalculator::NETT_CALCULATION
     *
     * PPh 21 ditanggung oleh perusahaan atau penyedia kerja.
     *
     * @var string
     */
    const NETT_CALCULATION = 'NETT';

    /**
     * PayrollCalculator::GROSS_CALCULATION
     *
     * PPh 21 ditanggung oleh pekerja/karyawan.
     *
     * @var string
     */
    const GROSS_CALCULATION = 'GROSS';

    /**
     * PayrollCalculator::GROSS_UP_CALCULATION
     *
     * Tanggungan PPh 21 ditambahkan sebagai tunjangan pekerja/karyawan.
     *
     * @var string
     */
    const GROSS_UP_CALCULATION = 'GROSSUP';

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

    /**
     * PayrollCalculator::$taxNumber
     * 
     * @var int 
     */
    public $taxNumber = 21;

    /**
     * PayrollCalculator::$method
     *
     * @var string
     */
    public $method = 'NETTO';

    /**
     * PayrollCalculator::$result
     *
     * @var SplArrayObject
     */
    public $result;

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
        $this->result = new SplArrayObject([
            'earnings' => new SplArrayObject([
                'base' => 0,
                'fixedAllowance' => 0,
                'annualy' => new SplArrayObject([
                    'nett' => 0,
                    'gross' => 0
                ])
            ]),
            'takeHomePay' => 0
        ]);
    }

    // ------------------------------------------------------------------------
    
    /**
     * PayrollCalculator::getCalculation
     *
     * @return \O2System\Spl\DataStructures\SplArrayObject
     */
    public function getCalculation()
    {
        if($this->taxNumber == 21) {
            return $this->calculateBaseOnPph21();
        } elseif($this->taxNumber == 23) {
            return $this->calculateBaseOnPph23();
        }
    }

    // ------------------------------------------------------------------------

    /**
     * PayrollCalculator::calculateBaseOnPph21
     *
     * @return \O2System\Spl\DataStructures\SplArrayObject
     */
    private function calculateBaseOnPph21()
    {
        // Gaji + Penghasilan teratur
        $this->result->earnings->base = ($this->employee->earnings->base / $this->provisions->company->numOfWorkingDays) * $this->employee->presences->workDays;
        $this->result->earnings->fixedAllowance = $this->employee->earnings->fixedAllowance;

        // Penghasilan bruto bulanan merupakan gaji pokok ditambah tunjangan tetap
        $this->result->earnings->gross = $this->result->earnings->base + $this->employee->earnings->fixedAllowance;

        if($this->employee->calculateHolidayAllowance > 0) {
            $this->result->earnings->holidayAllowance = $this->employee->calculateHolidayAllowance * $this->result->earnings->gross;
        }

        // Penghasilan tidak teratur
        if($this->provisions->company->calculateOvertime === true) {
            //  Berdasarkan Kepmenakertrans No. 102/MEN/VI/2004
            if($this->employee->presences->overtime > 1) {
                $overtime1stHours = 1 * 1.5 * 1/173 * $this->result->earnings->gross;
                $overtime2ndHours = ($this->employee->presences->overtime - 1) * 2 * 1/173 * $this->result->earnings->gross;
                $this->result->earnings->overtime = $overtime1stHours + $overtime2ndHours;
            } else {
                $this->result->earnings->overtime = $this->employee->presences->overtime * 1.5 * 1/173 * $this->result->earnings->gross;
            }

            $this->result->earnings->overtime = floor($this->result->earnings->overtime);

            // Lembur ditambahkan sebagai pendapatan bruto bulanan
            $this->result->earnings->gross = $this->result->earnings->gross + $this->result->earnings->overtime;
        }

        $this->result->earnings->annualy->gross = $this->result->earnings->gross * 12;

        if ($this->provisions->company->calculateBPJSKesehatan === true) {
            // Calculate BPJS Kesehatan Allowance & Deduction
            if ($this->employee->allowances->count()) {
                $this->employee->allowances->BPJSKesehatan = ($this->result->earnings->gross + $this->employee->allowances->getSum()) * (4 / 100);
                $this->employee->deductions->BPJSKesehatan = ($this->result->earnings->gross + $this->employee->allowances->getSum()) * (1 / 100);
            } else {
                $this->employee->allowances->BPJSKesehatan = $this->result->earnings->gross * (4 / 100);
                $this->employee->deductions->BPJSKesehatan = $this->result->earnings->gross * (1 / 100);
            }

            // Maximum number of dependents family is 5
            if ($this->employee->numOfDependentsFamily > 5) {
                $this->employee->deductions->BPJSKesehatan = $this->employee->deductions->BPJSKesehatan + ($this->employee->deductions->BPJSKesehatan * ($this->employee->numOfDependentsFamily - 5));
            }
        }

        // Calculate BPJS Ketenagakerjaan
        if($this->provisions->company->calculateBPJSKetenagakerjaan === true) {
            if ($this->result->earnings->gross < $this->provisions->state->highestWage) {

                $this->employee->allowances->JKK = $this->result->earnings->gross * $this->provisions->state->getJKKRiskGradePercentage($this->provisions->company->riskGrade);

                /**
                 * Perhitungan JKM
                 *
                 * Iuran jaminan kematian (JKM) sebesar 0,30% dari upah sebulan.
                 * Ditanggung sepenuhnya oleh perusahaan.
                 */
                $this->employee->allowances->JKM = $this->result->earnings->gross * (0.30 / 100);

                /**
                 * Perhitungan JHT
                 *
                 * Iuran jaminan hari tua (JHT) sebesar 5,7% dari upah sebulan,
                 * dengan ketentuan 3,7% ditanggung oleh pemberi kerja dan 2% ditanggung oleh pekerja.
                 */
                $this->employee->allowances->JHT = $this->result->earnings->gross * (3.7 / 100);
                $this->employee->deductions->JHT = $this->result->earnings->gross * (2 / 100);

                /**
                 * Perhitungan JP
                 *
                 * Iuran jaminan pensiun (JP) sebesar 3% dari upah sebulan,
                 * dengan ketentuan 2% ditanggung oleh pemberi kerja dan 1% ditanggung oleh pekerja.
                 */
                $this->employee->allowances->JIP = $this->result->earnings->gross * (2 / 100);
                $this->employee->deductions->JIP = $this->result->earnings->gross * (1 / 100);

            } elseif ($this->result->earnings->gross >= $this->provisions->state->provinceMinimumWage && $this->result->earnings->gross >= $this->provisions->state->highestWage) {
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

        $monthlyPositionDeduction = 0;
        if ($this->result->earnings->gross > $this->provisions->state->provinceMinimumWage) {

            /**
             * According to Undang-Undang Direktur Jenderal Pajak Nomor PER-32/PJ/2015 Pasal 21 ayat 3
             * Position Deduction is 5% from Annual Gross Income
             */
            $monthlyPositionDeduction = $this->result->earnings->gross * (5 / 100);

            /**
             * Maximum Position Deduction in Indonesia is 500000 / month
             * or 6000000 / year
             */
            if ($monthlyPositionDeduction >= 500000) {
                $monthlyPositionDeduction = 500000;
            }

            $this->employee->deductions->offsetSet('position', $monthlyPositionDeduction);
        }

        // Pendapatan bersih
        $this->result->earnings->nett = $this->result->earnings->gross + $this->employee->allowances->count() - $this->employee->deductions->count();
        $this->result->earnings->annualy->nett = $this->result->earnings->nett * 12;

        $this->result->offsetSet('taxable', (new Pph21($this))->calculate());

        switch ($this->method) {
            // Pajak ditanggung oleh perusahaan
            case self::NETT_CALCULATION:
                $takeHomePay = $monthlyNetIncome;
                break;
            // Pajak ditanggung oleh karyawan
            case self::GROSS_CALCULATION:
                $takeHomePay = $monthlyNetIncome - $tax->liability->monthly;
                $this->employee->deductions->offsetSet('PPH' . $this->taxNumber, $tax->result->liability->monthly);
                break;
            // Pajak ditanggung oleh perusahaan sebagai tunjangan pajak.
            case self::GROSS_UP_CALCULATION:
                $this->employee->allowances->offsetSet('PPH' . $this->taxNumber, $tax->result->liability->monthly);
                $takeHomePay = $monthlyNetIncome;
                break;
        }

        // Pengurangan Penalty
        $this->employee->deductions->offsetSet('penalty', new SplArrayObject([
            'late' => $this->employee->presences->latetime * $this->provisions->company->latetimePenalty,
            'absent' => $this->employee->presences->absentDays * $this->provisions->company->absentPenalty
        ]));

        $this->result->offsetSet('allowances', $this->employee->allowances);
        $this->result->offsetSet('bonus', $this->employee->bonus);
        $this->result->offsetSet('deductions', $this->employee->deductions);

        $this->result->takeHomePay = $this->result->earnings->nett - $this->employee->deductions->getSum();

        return $this->result;
    }

    // ------------------------------------------------------------------------

    /**
     * PayrollCalculator::calculateBaseOnPph23
     *
     * @return \O2System\Spl\DataStructures\SplArrayObject
     */
    private function calculateBaseOnPph23()
    {
        $tax = new Pph23($this);
        return $this->result;
    }
}