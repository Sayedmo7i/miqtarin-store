class Blog {
  int? id;
  String? title;
  String? slug;
  String? storeSlug;
  String? shortDescription;
  String? description;
  String? image;
  int? categoryId;
  String? categoryName;
  String? metaTitle;
  String? metaDescription;
  String? metaKeywords;
  String? createdAt;
  String? shareUrl;

  Blog({
    this.id,
    this.title,
    this.slug,
    this.storeSlug,
    this.shortDescription,
    this.description,
    this.image,
    this.categoryId,
    this.categoryName,
    this.metaTitle,
    this.metaDescription,
    this.metaKeywords,
    this.createdAt,
    this.shareUrl,
  });

  Blog.fromJson(Map<String, dynamic> json) {
    id = json['id'];
    title = json['title']?.toString();
    slug = json['slug']?.toString();
    storeSlug = json['store_slug']?.toString();
    shortDescription = json['short_description']?.toString();
    description = json['description']?.toString();
    image = json['image']?.toString();
    categoryId = json['category_id'];
    categoryName = json['category_name']?.toString();
    metaTitle = json['meta_title']?.toString();
    metaDescription = json['meta_description']?.toString();
    metaKeywords = json['meta_keywords']?.toString();
    createdAt = json['created_at']?.toString();
    shareUrl = json['share_url']?.toString();
  }
}
