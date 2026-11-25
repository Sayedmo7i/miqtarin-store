class Language {
final int? id;
final String? language;
final String? nativeLanguage;
final String? code;
final int? isRtl;
final String? updatedAt;

Language({
this.id,
this.language,
this.nativeLanguage,
this.code,
this.isRtl,
this.updatedAt,
});

Language copyWith({
int? id,
String? language,
String? nativeLanguage,
String? code,
int? isRtl,
String? updatedAt,
}) {
return Language(
id: id ?? this.id,
language: language ?? this.language,
nativeLanguage: nativeLanguage ?? this.nativeLanguage,
code: code ?? this.code,
isRtl: isRtl ?? this.isRtl,
updatedAt: updatedAt ?? this.updatedAt,
);
}

Language.fromJson(Map<String, dynamic> json)
: id = json['id'] as int?,
language = json['language'] as String?,
nativeLanguage = json['native_language'] as String?,
code = json['code'] as String?,
isRtl = json['is_rtl'] as int?,
updatedAt = json['updated_at'] as String?;

Map<String, dynamic> toJson() => {
'id': id,
'language': language,
'native_language': nativeLanguage,
'code': code,
'is_rtl': isRtl,
'updated_at': updatedAt
};

bool isThisRTL() => (isRtl == 1);
}
