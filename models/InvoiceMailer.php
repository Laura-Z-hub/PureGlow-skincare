<?php
declare(strict_types=1);

require_once __DIR__ . '/EmailSender.php';

final class InvoiceMailer
{
    public static function sendForOrder(PDO $pdo, int $orderId): bool
    {
        $order = self::getOrderWithCustomer($pdo, $orderId);

        if (!$order || !(bool)($order['invoice_email_requested'] ?? false) || !empty($order['invoice_sent_at'])) {
            return false;
        }

        $email = trim((string)($order['customer_email'] ?? ''));

        if (!validateEmail($email)) {
            return false;
        }

        $items = self::getOrderItems($pdo, $orderId);
        $subject = 'PureGlow invoice for order ' . $order['order_number'];
        $message = self::buildInvoiceMessage($order, $items);
        $sent = EmailSender::sendHtml($email, $subject, $message);

        if ($sent) {
            $stmt = $pdo->prepare('UPDATE orders SET invoice_sent_at = NOW() WHERE id = :id');
            $stmt->execute(['id' => $orderId]);
        }

        return $sent;
    }

    private static function getOrderWithCustomer(PDO $pdo, int $orderId): ?array
    {
        $stmt = $pdo->prepare(
            'SELECT o.*, u.name AS customer_name, u.email AS customer_email
            FROM orders o
            INNER JOIN users u ON u.id = o.user_id
            WHERE o.id = :id
            LIMIT 1'
        );
        $stmt->execute(['id' => $orderId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        return $order ?: null;
    }

    private static function getOrderItems(PDO $pdo, int $orderId): array
    {
        $stmt = $pdo->prepare(
            'SELECT oi.quantity, oi.unit_price, oi.total_price, p.name, p.brand, p.sku
            FROM order_items oi
            LEFT JOIN products p ON p.id = oi.product_id
            WHERE oi.order_id = :order_id
            ORDER BY oi.id ASC'
        );
        $stmt->execute(['order_id' => $orderId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private static function buildInvoiceMessage(array $order, array $items): string
    {
        $address = json_decode((string)($order['shipping_address'] ?? '{}'), true);
        $address = is_array($address) ? $address : [];
        $currency = self::e((string)($order['currency'] ?? 'EUR'));
        $shippingAddress = trim(implode(', ', array_filter([
            $address['street'] ?? '',
            $address['city'] ?? '',
            $address['postal_code'] ?? '',
            $address['country'] ?? '',
        ])));

        $itemRows = '';
        foreach ($items as $item) {
            $name = self::e((string)($item['name'] ?: 'Product'));
            $brand = self::e((string)($item['brand'] ?: 'PureGlow'));
            $quantity = (int)$item['quantity'];
            $unitPrice = number_format((float)$item['unit_price'], 2);
            $lineTotal = number_format((float)$item['total_price'], 2);

            $itemRows .= '
                <tr>
                    <td style="padding:14px 0;border-bottom:1px solid #efe4d7;">
                        <div style="font-weight:800;color:#2e2822;">' . $name . '</div>
                        <div style="margin-top:3px;font-size:13px;color:#9f8b7c;">' . $brand . '</div>
                    </td>
                    <td style="padding:14px 0;border-bottom:1px solid #efe4d7;text-align:center;color:#6c5241;font-weight:800;">' . $quantity . '</td>
                    <td style="padding:14px 0;border-bottom:1px solid #efe4d7;text-align:right;color:#6c5241;">' . $currency . ' ' . $unitPrice . '</td>
                    <td style="padding:14px 0;border-bottom:1px solid #efe4d7;text-align:right;color:#2e2822;font-weight:900;">' . $currency . ' ' . $lineTotal . '</td>
                </tr>';
        }

        return '<!doctype html>
<html>
<body style="margin:0;padding:0;background:#fcf8f2;font-family:Arial,Helvetica,sans-serif;color:#2e2822;">
  <div style="padding:28px 14px;">
    <div style="max-width:680px;margin:0 auto;background:#fffdf9;border:1px solid #eadccf;border-radius:22px;overflow:hidden;box-shadow:0 18px 46px rgba(46,40,34,.12);">
      <div style="height:7px;background:linear-gradient(90deg,#f2e1c9,#d5b089,#f2e1c9);"></div>
      <div style="padding:30px;">
        <div style="display:inline-block;padding:9px 13px;border-radius:14px;background:#f7ead8;color:#6c5241;font-weight:900;letter-spacing:.08em;text-transform:uppercase;font-size:12px;">Invoice confirmed</div>
        <h1 style="margin:18px 0 8px;font-family:Georgia,serif;font-size:36px;line-height:1;color:#2e2822;font-weight:500;">Thank you for your order</h1>
        <p style="margin:0 0 24px;color:#6c5241;line-height:1.7;font-size:15px;">Hi ' . self::e((string)($order['customer_name'] ?? 'there')) . ', your PureGlow order has been confirmed. Your invoice details are below.</p>

        <div style="background:#fcf8f2;border:1px solid #efe4d7;border-radius:18px;padding:16px;margin-bottom:22px;">
          <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;">
            <tr>
              <td style="padding:4px 0;color:#9f8b7c;font-size:13px;font-weight:800;">Order</td>
              <td style="padding:4px 0;text-align:right;color:#2e2822;font-weight:900;">' . self::e((string)$order['order_number']) . '</td>
            </tr>
            <tr>
              <td style="padding:4px 0;color:#9f8b7c;font-size:13px;font-weight:800;">Date</td>
              <td style="padding:4px 0;text-align:right;color:#6c5241;">' . self::e((string)$order['created_at']) . '</td>
            </tr>
            <tr>
              <td style="padding:4px 0;color:#9f8b7c;font-size:13px;font-weight:800;">Status</td>
              <td style="padding:4px 0;text-align:right;color:#6f8a7a;font-weight:900;">Confirmed</td>
            </tr>
          </table>
        </div>

        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;margin-bottom:20px;">
          <thead>
            <tr>
              <th align="left" style="padding:0 0 10px;color:#9f8b7c;font-size:12px;text-transform:uppercase;letter-spacing:.08em;">Product</th>
              <th align="center" style="padding:0 0 10px;color:#9f8b7c;font-size:12px;text-transform:uppercase;letter-spacing:.08em;">Qty</th>
              <th align="right" style="padding:0 0 10px;color:#9f8b7c;font-size:12px;text-transform:uppercase;letter-spacing:.08em;">Price</th>
              <th align="right" style="padding:0 0 10px;color:#9f8b7c;font-size:12px;text-transform:uppercase;letter-spacing:.08em;">Total</th>
            </tr>
          </thead>
          <tbody>' . $itemRows . '</tbody>
        </table>

        <div style="text-align:right;margin:10px 0 24px;">
          <div style="color:#9f8b7c;font-size:13px;font-weight:800;text-transform:uppercase;letter-spacing:.08em;">Order total</div>
          <div style="margin-top:4px;color:#2e2822;font-size:28px;font-weight:900;">' . $currency . ' ' . number_format((float)$order['total_amount'], 2) . '</div>
        </div>

        <div style="background:#fff;border:1px solid #efe4d7;border-radius:18px;padding:16px;">
          <div style="color:#2e2822;font-weight:900;margin-bottom:6px;">Shipping address</div>
          <div style="color:#6c5241;line-height:1.6;">' . self::e($shippingAddress !== '' ? $shippingAddress : 'Not provided') . '</div>
        </div>

        <p style="margin:24px 0 0;color:#9f8b7c;line-height:1.7;font-size:13px;">Thank you for shopping with PureGlow. We hope your skincare ritual feels a little more special today.</p>
      </div>
    </div>
  </div>
</body>
</html>';
    }

    private static function e(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}
