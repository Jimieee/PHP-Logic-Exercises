<?php
declare(strict_types=1);

// ---- Constants (used by call cost) ----
const ZONE_PRICES = [
    12 => 2.00, // North America
    15 => 2.20, // Central America
    18 => 4.50, // South America
    19 => 3.50, // Europe
    23 => 6.00, // Asia
    25 => 6.00, // Africa
    29 => 5.00, // Oceania
];

const ZONE_NAMES = [
    12 => 'América del Norte',
    15 => 'América Central',
    18 => 'América del Sur',
    19 => 'Europa',
    23 => 'Asia',
    25 => 'África',
    29 => 'Oceanía',
];

// ---- Small helpers (console display) ----
function printTitleEs(string $title): void {
    echo PHP_EOL, str_repeat('=', 10), ' ', $title, ' ', str_repeat('=', 10), PHP_EOL;
}

function printArrayInline(array $data): void {
    echo '[' . implode(', ', array_map(static fn($v) => (string)$v, $data)) . ']', PHP_EOL;
}

// ---- Part 1 ----

/** Generate first n Fibonacci terms (iterative, O(n)). */
function generateFibonacci(int $n): array {
    if ($n <= 0) return [];
    if ($n === 1) return [0];
    $seq = [0, 1];
    for ($i = 2; $i < $n; $i++) {
        $seq[] = $seq[$i - 1] + $seq[$i - 2];
    }
    return $seq;
}

/** Return true if value is prime (trial division up to sqrt(n)). */
function isPrime(int $value): bool {
    if ($value < 2) return false;
    if ($value % 2 === 0) return $value === 2;
    $limit = (int)floor(sqrt($value));
    for ($d = 3; $d <= $limit; $d += 2) {
        if ($value % $d === 0) return false;
    }
    return true;
}

/** Check if text is a palindrome ignoring spaces/punctuation (UTF‑8 aware). */
function isPalindrome(string $text): bool {
    $lower = function_exists('mb_strtolower') ? mb_strtolower($text, 'UTF-8') : strtolower($text);
    $normalized = preg_replace('/[^\p{L}\p{N}]+/u', '', $lower);
    if ($normalized === null) $normalized = preg_replace('/[^a-z0-9]+/i', '', $lower) ?? '';
    $chars = preg_split('//u', $normalized, -1, PREG_SPLIT_NO_EMPTY) ?: [];
    return $normalized === implode('', array_reverse($chars));
}

// ---- Part 2 ----

/** Sum only even integers in the array. */
function sumEvenNumbers(array $numbers): int {
    $sum = 0;
    foreach ($numbers as $n) {
        if ($n % 2 === 0) $sum += $n;
    }
    return $sum;
}

/** Compute international call total cost. 10% discount if minutes < 30. */
function computeCallCost(int $zoneKey, int $minutes): float {
    if ($minutes < 0) throw new InvalidArgumentException('Minutes must be >= 0');
    if (!isset(ZONE_PRICES[$zoneKey])) throw new InvalidArgumentException('Unknown zone key');
    $base = ZONE_PRICES[$zoneKey] * $minutes;
    $total = ($minutes < 30) ? $base * 0.9 : $base;
    return round($total, 2);
}

/** Classic FizzBuzz: 1 <= n <= 10_000. */
function fizzBuzz(int $n): array {
    if ($n < 1 || $n > 10000) throw new InvalidArgumentException('n out of range');
    $ans = [];
    for ($i = 1; $i <= $n; $i++) {
        if ($i % 15 === 0) $ans[] = 'FizzBuzz';
        elseif ($i % 3 === 0) $ans[] = 'Fizz';
        elseif ($i % 5 === 0) $ans[] = 'Buzz';
        else $ans[] = (string)$i;
    }
    return $ans;
}

// ---- Demo (Spanish console output) ----
if (PHP_SAPI === 'cli' && realpath($argv[0]) === __FILE__) {
    printTitleEs('Parte 1 - Serie de Fibonacci');
    echo 'Primeros 7 términos: ';
    printArrayInline(generateFibonacci(7)); // [0, 1, 1, 2, 3, 5, 8]

    printTitleEs('Parte 1 - Números Primos');
    foreach ([1, 2, 3, 4, 17, 18, 19, 20] as $v) {
        echo $v, ' => ', isPrime((int)$v) ? 'primo' : 'no primo', PHP_EOL;
    }

    printTitleEs('Parte 1 - Palíndromos');
    foreach (['reconocer', 'Anita lava la tina', 'No es palindromo'] as $t) {
        echo '"', $t, '" => ', isPalindrome($t) ? 'sí' : 'no', PHP_EOL;
    }

    printTitleEs('Parte 2 - Suma de Números Pares en Arreglo');
    $arr = [1, 2, 3, 4, 5, 6];
    echo 'Arreglo: ';
    printArrayInline($arr);
    echo 'Suma de pares: ', sumEvenNumbers($arr), PHP_EOL;

    printTitleEs('Parte 2 - Costo de Llamadas Internacionales');
    $tests = [[12, 25], [19, 45]]; // [zoneKey, minutes]
    foreach ($tests as [$zone, $min]) {
        $name = ZONE_NAMES[$zone] ?? 'Desconocida';
        $total = number_format(computeCallCost($zone, $min), 2);
        echo "Zona: {$name} (clave {$zone}) | Minutos: {$min} => Total: $", $total, PHP_EOL;
    }

    printTitleEs('Parte 2 - FizzBuzz (n = 15)');
    printArrayInline(fizzBuzz(15));
}
