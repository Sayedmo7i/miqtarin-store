
class DeliverabilityResponse {
  final String message;
  final List<DeliverableProduct>? products;
  final double? cartSubtotal;
  final double? deliveryChargeWithoutCod;
  final double? deliveryChargeWithCod;
  final double? codExtraCharge;
  final double? finalTotalWithoutCod;
  final double? finalTotalWithCod;
  final ShippingOptions? shippingOptions;
  final String? defaultShippingOption;
  final CurrencyInfo? currency;

  // Formatted currency strings
  final String? currencyCartSubtotal;
  final String? currencyDeliveryChargeWithoutCod;
  final String? currencyDeliveryChargeWithCod;
  final String? currencyFinalTotalWithoutCod;
  final String? currencyFinalTotalWithCod;

  DeliverabilityResponse({
    required this.message,
    this.products,
    this.cartSubtotal,
    this.deliveryChargeWithoutCod,
    this.deliveryChargeWithCod,
    this.codExtraCharge,
    this.finalTotalWithoutCod,
    this.finalTotalWithCod,
    this.shippingOptions,
    this.defaultShippingOption,
    this.currency,
    this.currencyCartSubtotal,
    this.currencyDeliveryChargeWithoutCod,
    this.currencyDeliveryChargeWithCod,
    this.currencyFinalTotalWithoutCod,
    this.currencyFinalTotalWithCod,
  });

  factory DeliverabilityResponse.fromJson(Map<String, dynamic> json) {
    List<DeliverableProduct>? products;
    if (json['data'] != null) {
      products = <DeliverableProduct>[];
      json['data'].forEach((v) {
        products!.add(DeliverableProduct.fromJson(v));
      });
    }

    return DeliverabilityResponse(
      message: json['message'] ?? '',
      products: products,
      cartSubtotal: _parseDouble(json['cart_subtotal']),
      deliveryChargeWithoutCod: _parseDouble(
        json['delivery_charge_without_cod'],
      ),
      deliveryChargeWithCod: _parseDouble(json['delivery_charge_with_cod']),
      codExtraCharge: _parseDouble(json['cod_extra_charge']),
      finalTotalWithoutCod: _parseDouble(json['final_total_without_cod']),
      finalTotalWithCod: _parseDouble(json['final_total_with_cod']),
      shippingOptions: json['shipping_options'] != null
          ? ShippingOptions.fromJson(json['shipping_options'])
          : null,
      defaultShippingOption: json['default_shipping_option'],
      currency: json['currency'] != null
          ? CurrencyInfo.fromJson(json['currency'])
          : null,
      currencyCartSubtotal: json['currency_cart_subtotal'],
      currencyDeliveryChargeWithoutCod:
          json['currency_delivery_charge_without_cod'],
      currencyDeliveryChargeWithCod: json['currency_delivery_charge_with_cod'],
      currencyFinalTotalWithoutCod: json['currency_final_total_without_cod'],
      currencyFinalTotalWithCod: json['currency_final_total_with_cod'],
    );
  }

  static double? _parseDouble(dynamic value) {
    if (value == null) return null;
    if (value is double) return value;
    if (value is int) return value.toDouble();
    return double.tryParse(value.toString());
  }
}

class DeliverableProduct {
  final bool isDeliverable;
  final String? deliveryBy;
  final String? estimateDate;
  final String? message;
  final int? productId;
  final int? productQty;
  final int? minimumFreeDeliveryOrderQty;
  final double? productDeliveryCharge;
  final int? variantId;
  final String? name;

  DeliverableProduct({
    required this.isDeliverable,
    this.deliveryBy,
    this.estimateDate,
    this.message,
    this.productId,
    this.productQty,
    this.minimumFreeDeliveryOrderQty,
    this.productDeliveryCharge,
    this.variantId,
    this.name,
  });

  factory DeliverableProduct.fromJson(Map<String, dynamic> json) {
    return DeliverableProduct(
      isDeliverable: json['is_deliverable'] ?? false,
      deliveryBy: json['delivery_by'],
      estimateDate: json['estimate_date'],
      message: json['message'],
      productId: json['product_id'],
      productQty: json['product_qty'],
      minimumFreeDeliveryOrderQty: json['minimum_free_delivery_order_qty'],
      productDeliveryCharge: _parseDouble(json['product_delivery_charge']),
      variantId: json['variant_id'],
      name: json['name'],
    );
  }

  static double? _parseDouble(dynamic value) {
    if (value == null) return null;
    if (value is double) return value;
    if (value is int) return value.toDouble();
    return double.tryParse(value.toString());
  }
}

class ShippingOptions {
  final ShippingOption? recommended;
  final ShippingOption? cheapest;
  final ShippingOption? fastest;

  ShippingOptions({this.recommended, this.cheapest, this.fastest});

  factory ShippingOptions.fromJson(Map<String, dynamic> json) {
    return ShippingOptions(
      recommended: json['recommended'] != null
          ? ShippingOption.fromJson(json['recommended'])
          : null,
      cheapest: json['cheapest'] != null
          ? ShippingOption.fromJson(json['cheapest'])
          : null,
      fastest: json['fastest'] != null
          ? ShippingOption.fromJson(json['fastest'])
          : null,
    );
  }

  ShippingOption? getSelectedOption(String? defaultOption) {
    switch (defaultOption) {
      case 'recommended':
        return recommended;
      case 'cheapest':
        return cheapest;
      case 'fastest':
        return fastest;
      default:
        return recommended;
    }
  }
}

class ShippingOption {
  final double? rate;
  final String? etd;
  final String? days;
  final String? courier;
  final String? formatRate;
  final bool isSelected;

  ShippingOption({
    this.rate,
    this.etd,
    this.days,
    this.courier,
    this.formatRate,
    this.isSelected = false,
  });

  factory ShippingOption.fromJson(Map<String, dynamic> json) {
    return ShippingOption(
      rate: _parseDouble(json['rate']),
      etd: json['etd'],
      days: json['days'],
      courier: json['courier'],
      formatRate: json['format_rate'],
      isSelected: json['is_selected'] ?? false,
    );
  }

  static double? _parseDouble(dynamic value) {
    if (value == null) return null;
    if (value is double) return value;
    if (value is int) return value.toDouble();
    return double.tryParse(value.toString());
  }
}

class CurrencyInfo {
  final String? code;
  final String? symbol;

  CurrencyInfo({this.code, this.symbol});

  factory CurrencyInfo.fromJson(Map<String, dynamic> json) {
    return CurrencyInfo(code: json['code'], symbol: json['symbol']);
  }
}
