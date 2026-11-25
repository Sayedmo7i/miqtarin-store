class Brand {
  int? id;
  int? sellerId;
  int? storeId;
  String? name;
  String? slug;
  String? image;
  int? status;
  Brand({
    required this.id,
    this.sellerId,
    this.storeId,
    required this.name,
    this.slug,
    required this.image,
    required this.status,
  });
  Brand.fromJson(Map<String, dynamic> json) {
    id = json["id"];
    sellerId = json["seller_id"];
    storeId = int.tryParse(json["store_id"].toString());
    name = json["name"];
    slug = json["slug"];
    image = json["image"];
    status = json["status"];
  }
}
