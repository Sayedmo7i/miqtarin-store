class BlogCategory {
  int? id;
  String? name;
  String? slug;
  String? image;
  String? createdAt;

  BlogCategory({
    this.id,
    this.name,
    this.slug,
    this.image,
    this.createdAt,
  });

  BlogCategory.fromJson(Map<String, dynamic> json) {
    id = json['id'];
    name = json['name']?.toString();
    slug = json['slug']?.toString();
    image = json['image']?.toString();
    createdAt = json['created_at']?.toString();
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = <String, dynamic>{};
    data['id'] = id;
    data['name'] = name;
    data['slug'] = slug;
    data['image'] = image;
    data['created_at'] = createdAt;
    return data;
  }
}
