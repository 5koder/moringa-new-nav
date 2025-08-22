<?php
/**
 * Overrides carrier shipping with Table Rate Shipping
 *
 * Table Rate Shipping by Kahanit(https://www.kahanit.com/) is licensed under a
 * Creative Creative Commons Attribution-NoDerivatives 4.0 International License.
 * Based on a work at https://www.kahanit.com/.
 * Permissions beyond the scope of this license may be available at https://www.kahanit.com/.
 * To view a copy of this license, visit http://creativecommons.org/licenses/by-nd/4.0/.
 *
 * @author    Amit Sidhpura <amit@kahanit.com>
 * @copyright 2016 Kahanit
 * @license   http://creativecommons.org/licenses/by-nd/4.0/
 */

use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

class TRSExpressionFunctionProvider implements ExpressionFunctionProviderInterface
{
    public function getFunctions()
    {
        return [
            $this->getFunctionSin(),
            $this->getFunctionCos(),
            $this->getFunctionTan(),
            $this->getFunctionCsc(),
            $this->getFunctionSec(),
            $this->getFunctionCot(),
            $this->getFunctionAbs(),
            $this->getFunctionLog(),
            $this->getFunctionLog10(),
            $this->getFunctionSqrt(),
            $this->getFunctionCeil(),
            $this->getFunctionFloor()
        ];
    }

    public function getFunctionSin()
    {
        return new ExpressionFunction('sin', function ($num) {
            return sprintf('(is_numeric(%1$s) ? sin(%1$s) : %1$s)', $num);
        }, function ($arguments, $num) {
            if (!is_numeric($num)) {
                return $num;
            }

            return sin($num);
        });
    }

    public function getFunctionCos()
    {
        return new ExpressionFunction('cos', function ($num) {
            return sprintf('(is_numeric(%1$s) ? cos(%1$s) : %1$s)', $num);
        }, function ($arguments, $num) {
            if (!is_numeric($num)) {
                return $num;
            }

            return cos($num);
        });
    }

    public function getFunctionTan()
    {
        return new ExpressionFunction('tan', function ($num) {
            return sprintf('(is_numeric(%1$s) ? tan(%1$s) : %1$s)', $num);
        }, function ($arguments, $num) {
            if (!is_numeric($num)) {
                return $num;
            }

            return tan($num);
        });
    }

    public function getFunctionCsc()
    {
        return new ExpressionFunction('csc', function ($num) {
            return sprintf('(is_numeric(%1$s) ? csc(%1$s) : %1$s)', $num);
        }, function ($arguments, $num) {
            if (!is_numeric($num)) {
                return $num;
            }

            $tmp = sin($num);
            if ($tmp == 0) {
                throw new \Exception("Division by 0 on: 'csc({$num}) = 1/sin({$num})'", 5501);
            }

            return 1 / $tmp;
        });
    }

    public function getFunctionSec()
    {
        return new ExpressionFunction('sec', function ($num) {
            return sprintf('(is_numeric(%1$s) ? sec(%1$s) : %1$s)', $num);
        }, function ($arguments, $num) {
            if (!is_numeric($num)) {
                return $num;
            }

            $tmp = cos($num);
            if ($tmp == 0) {
                throw new \Exception("Division by 0 on: 'sec({$num}) = 1/cos({$num})'", 5501);
            }

            return 1 / $tmp;
        });
    }

    public function getFunctionCot()
    {
        return new ExpressionFunction('cot', function ($num) {
            return sprintf('(is_numeric(%1$s) ? cot(%1$s) : %1$s)', $num);
        }, function ($arguments, $num) {
            if (!is_numeric($num)) {
                return $num;
            }

            $tmp = tan($num);
            if ($tmp == 0) {
                throw new \Exception("Division by 0 on: 'cot({$num}) = 1/tan({$num})'", 5501);
            }

            return 1 / $tmp;
        });
    }

    public function getFunctionAbs()
    {
        return new ExpressionFunction('abs', function ($num) {
            return sprintf('(is_numeric(%1$s) ? abs(%1$s) : %1$s)', $num);
        }, function ($arguments, $num) {
            if (!is_numeric($num)) {
                return $num;
            }

            return abs($num);
        });
    }

    public function getFunctionLog()
    {
        return new ExpressionFunction('log', function ($num) {
            return sprintf('(is_numeric(%1$s) ? log(%1$s) : %1$s)', $num);
        }, function ($arguments, $num) {
            if (!is_numeric($num)) {
                return $num;
            }

            $ans = log($num);
            if (is_nan($ans) || is_infinite($ans)) {
                throw new \Exception("Result of 'log({$num}) = {$ans}' is either infinite or a non-number", 5504);
            }

            return $ans;
        });
    }

    public function getFunctionLog10()
    {
        return new ExpressionFunction('log10', function ($num) {
            return sprintf('(is_numeric(%1$s) ? log10(%1$s) : %1$s)', $num);
        }, function ($arguments, $num) {
            if (!is_numeric($num)) {
                return $num;
            }

            $ans = log10($num);
            if (is_nan($ans) || is_infinite($ans)) {
                throw new \Exception("Result of 'log10({$num}) = {$ans}' is either infinite or a non-number", 5504);
            }

            return $ans;
        });
    }

    public function getFunctionSqrt()
    {
        return new ExpressionFunction('sqrt', function ($num) {
            return sprintf('(is_numeric(%1$s) ? sqrt(%1$s) : %1$s)', $num);
        }, function ($arguments, $num) {
            if (!is_numeric($num)) {
                return $num;
            }

            return sqrt($num);
        });
    }

    public function getFunctionCeil()
    {
        return new ExpressionFunction('ceil', function ($num) {
            return sprintf('(is_numeric(%1$s) ? ceil(%1$s) : %1$s)', $num);
        }, function ($arguments, $num) {
            if (!is_numeric($num)) {
                return $num;
            }

            return ceil($num);
        });
    }

    public function getFunctionFloor()
    {
        return new ExpressionFunction('floor', function ($num) {
            return sprintf('(is_numeric(%1$s) ? floor(%1$s) : %1$s)', $num);
        }, function ($arguments, $num) {
            if (!is_numeric($num)) {
                return $num;
            }

            return floor($num);
        });
    }
}
