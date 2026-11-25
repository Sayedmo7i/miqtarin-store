class Location {
  String? id,
      pickupLocation,
      pincode,
      name,
      email,
      phone,
      address,
      address2,
      city,
      state,
      country;
  int? status;

  bool get isVerified => status == 1;

  Location({
    this.id,
    this.name,
    this.email,
    this.phone,
    this.pincode,
    this.address,
    this.address2,
    this.city,
    this.state,
    this.country,
    this.pickupLocation,
    this.status,
  });

  Location.fromJson(Map<String, dynamic> json) {
    id = json["id"].toString();
    pickupLocation = json["pickup_location"];
    name = json["name"];
    email = json["email"];
    phone = json["phone"];
    address = json["address"];
    address2 = json["address2"];
    pincode = json["pincode"];
    city = json["city"] ?? "";
    state = json["state"] ?? "";
    country = json["country"] ?? "";
    status = json["status"] ?? 0;
  }
}
