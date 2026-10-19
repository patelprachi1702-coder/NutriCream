<?php

function getOrderedProductSummary(mysqli $conn): array
{
    $products = [];

    foreach (['online_orders', 'offline_orders'] as $orderType) {
        $result = mysqli_query($conn, "SELECT id, items FROM {$orderType} ORDER BY id ASC");
        if ($result === false) {
            die('Ordered product lookup failed: ' . mysqli_error($conn));
        }

        while ($order = mysqli_fetch_assoc($result)) {
            $items = json_decode($order['items'], true);
            if (!is_array($items)) {
                continue;
            }

            $productsInOrder = [];
            foreach ($items as $item) {
                if (!is_array($item)) {
                    continue;
                }

                $name = trim((string) ($item['name'] ?? ''));
                if ($name === '') {
                    continue;
                }

                $key = strtolower($name);
                if (!isset($products[$key])) {
                    $products[$key] = [
                        'name' => $name,
                        'quantity' => 0,
                        'online_orders' => 0,
                        'offline_orders' => 0,
                    ];
                }

                $quantity = filter_var($item['quantity'] ?? null, FILTER_VALIDATE_INT);
                if ($quantity !== false && $quantity > 0) {
                    $products[$key]['quantity'] += $quantity;
                }
                $productsInOrder[$key] = true;
            }

            $orderCountKey = $orderType === 'online_orders' ? 'online_orders' : 'offline_orders';
            foreach (array_keys($productsInOrder) as $key) {
                $products[$key][$orderCountKey]++;
            }
        }

        mysqli_free_result($result);
    }

    uasort($products, static function (array $first, array $second): int {
        return strcasecmp($first['name'], $second['name']);
    });

    return $products;
}
