<?php

namespace App\Http\Controllers\Delivery_boy;

use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cookie;


class LanguageController extends Controller
{
    public function change(Request $request)
    {

        $request->validate([
            'lang' => 'required|string|max:255',
        ]);

        $is_rtl = (int) (Language::where('code', $request->lang)->value('is_rtl') ?? (in_array($request->lang, ['ar', 'he', 'fa', 'ur'], true) ? 1 : 0));

        app()->setLocale($request->lang);

        session()->put('locale', $request->lang);
        session()->put('is_rtl', $is_rtl);
        Cookie::queue('mq_locale', $request->lang, 60 * 24 * 30);
        Cookie::queue('mq_is_rtl', (string) $is_rtl, 60 * 24 * 30);

        return redirect()->back();
    }

    public function setLanguage($locale)
    {
        config(['app.locale' => $locale]);
        $isRtl = (int) (Language::where('code', $locale)->value('is_rtl') ?? (in_array($locale, ['ar', 'he', 'fa', 'ur'], true) ? 1 : 0));
        session()->put('locale', $locale);
        session()->put('is_rtl', $isRtl);
        Cookie::queue('mq_locale', $locale, 60 * 24 * 30);
        Cookie::queue('mq_is_rtl', (string) $isRtl, 60 * 24 * 30);

        return redirect()->back();
    }


    function list()
    {
        $search = trim(request('search'));
        $sort = (request('sort')) ? request('sort') : "id";
        $order = (request('order')) ? request('order') : "DESC";
        $limit = (request('limit')) ? request('limit') : 5;
        $pageNumber = request('offset') / $limit + 1;


        $languages = language::query()->when($search, function ($query) use ($search) {
            return $query->where('name', 'like', '%' . $search . '%')
                ->orWhere('id', 'like', '%' . $search . '%')
                ->orWhere('code', 'like', '%' . $search . '%');
        })
            ->orderBy($sort, $order)
            ->paginate($limit, ['*'], 'page', $pageNumber);

        $languages->transform(function ($item) {
            $item['delete'] = $item['code'] == 'en' ?
                "" :
                '<button type="button" class="btn btn-danger btn-sm delete-button" data-id="' . $item['id'] . '">
                    <i class="fa fa-trash"> </i>
                </button>';
            return $item;
        });


        return response()->json([
            "rows" => $languages->items(),
            'total' => $languages->total()
        ]);
    }
}
