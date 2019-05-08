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
 * Class Ketenagakerjaan
 * @package Steevenz\IndonesiaPayrollCalculator\BPJS
 */
class Ketenagakerjaan extends AbstractBPJS
{
    /**
     * Ketenagakerjaan::$listOfJKKCalculationPercentageBasedOnRiskGrade
     *
     * Faktor penghitungan iuran jaminan kecelakaan kerja (JKK) berdasarkan
     * penggolongan kelompok resiko lingkungan kerja.
     *
     * @var array
     */
    protected $listOfJKKCalculationPercentageBasedOnRiskGrade = [
        1 => 0.24,
        2 => 0.54,
        3 => 0.89,
        4 => 1.27,
        5 => 1.74,
    ];

    /**
     * Ketenagakerjaan::$riskGrade
     *
     * @var int
     */
    protected $riskGrade = 2;

    // ------------------------------------------------------------------------

    /**
     * Ketenagakerjaan::setRiskGrade
     *
     * @param int $riskGrade
     *
     * @return static
     */
    public function setRiskGrade($riskGrade)
    {
        if (array_key_exists($riskGrade, $this->listOfJKKCalculationPercentageBasedOnRiskGrade)) {
            $this->riskGrade = (int)$riskGrade;
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Ketenagakerjaan::calculate
     *
     * @return array Returns array of BPJS Ketenagakerjaan allowance and deduction.
     */
    public function calculate()
    {
        if ($this->employeeGrossTotalIncome < $this->highestMonthlySalary) {

            $JKK = $this->employeeGrossTotalIncome * ($this->listOfJKKCalculationPercentageBasedOnRiskGrade[ $this->riskGrade ] / 100);

            /**
             * Perhitungan JKM
             *
             * Iuran jaminan kematian (JKM) sebesar 0,30% dari upah sebulan.
             * Ditanggung sepenuhnya oleh perusahaan.
             */
            $JKM = $this->employeeGrossTotalIncome * (0.30 / 100);

            /**
             * Perhitungan JHT
             *
             * Iuran jaminan hari tua (JHT) sebesar 5,7% dari upah sebulan,
             * dengan ketentuan 3,7% ditanggung oleh pemberi kerja dan 2% ditanggung oleh pekerja.
             */
            $JHTbyCompany = $this->employeeGrossTotalIncome * (3.7 / 100);
            $JHTbyEmployee = $this->employeeGrossTotalIncome * (2 / 100);

            /**
             * Perhitungan JP
             *
             * Iuran jaminan pensiun (JP) sebesar 3% dari upah sebulan,
             * dengan ketentuan 2% ditanggung oleh pemberi kerja dan 1% ditanggung oleh pekerja.
             */
            $JPbyCompany = $this->employeeGrossTotalIncome * (2 / 100);
            $JPbyEmployee = $this->employeeGrossTotalIncome * (1 / 100);

        } elseif ($this->employeeGrossTotalIncome >= $this->ump && $this->employeeGrossTotalIncome >= $this->highestMonthlySalary) {
            $JKK = $this->highestMonthlySalary * ($this->listOfJKKCalculationPercentageBasedOnRiskGrade[ $this->riskGrade ] / 100);


            /**
             * Perhitungan JKM
             *
             * Iuran jaminan kematian (JKM) sebesar 0,30% dari upah sebulan.
             * Ditanggung sepenuhnya oleh perusahaan.
             */
            $JKM = $this->highestMonthlySalary * (0.30 / 100);

            /**
             * Perhitungan JHT
             *
             * Iuran jaminan hari tua (JHT) sebesar 5,7% dari upah sebulan,
             * dengan ketentuan 3,7% ditanggung oleh pemberi kerja dan 2% ditanggung oleh pekerja.
             *
             * Dihitung dari batas nilai upah bulanan tertinggi, karena total income melebihi UMP.
             */
            $JHTbyCompany = $this->highestMonthlySalary * (3.7 / 100);
            $JHTbyEmployee = $this->highestMonthlySalary * (2 / 100);

            /**
             * Perhitungan JP
             *
             * Iuran jaminan pensiun (JP) sebesar 3% dari upah sebulan,
             * dengan ketentuan 2% ditanggung oleh pemberi kerja dan 1% ditanggung oleh pekerja.
             *
             * Maximum Perhitungan total income jaminan pensiun adalah 7.000.000
             */
            $JPbyCompany = 7000000 * (2 / 100);
            $JPbyEmployee = 7000000 * (1 / 100);
        }

        return [
            'allowance' => $JKK + $JKM + $JHTbyCompany + $JPbyCompany,
            'deduction' => $JHTbyEmployee + $JPbyEmployee,
        ];
    }
}