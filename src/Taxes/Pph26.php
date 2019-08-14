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

namespace Steevenz\IndonesiaPayrollCalculator\Taxes;

use O2System\Spl\DataStructures\SplArrayObject;

/**
 * Class Pph26
 * @package Steevenz\IndonesiaPayrollCalculator\Taxes
 */
class Pph26 extends AbstractPph
{
    /**
     * Pph26::calculate
     *
     * @return \O2System\Spl\DataStructures\SplArrayObject
     */
    public function calculate()
    {
        $this->result->offsetSet('liability', new SplArrayObject([
            'rule' => 26,
            'amount' => 0
        ]));
        $this->result->liability->amount = $this->calculator->employee->earnings->base * (20 / 100);

        // Jika tidak memiliki NPWP dikenakan tambahan 100%
        if($this->calculator->employee->hasNPWP === false) {
            $this->result->liability->amount = $this->result->liability->amount + ($this->result->liability->amount * 100/100);
        }

        return $this->result;
    }
}