<!doctype html>
<html>
<head>
    <title>RDW API - license plate check</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel=stylesheet
          href="https://cdn.jsdelivr.net/npm/pretty-print-json@3.0/dist/css/pretty-print-json.dark-mode.css">
    <script src="https://cdn.jsdelivr.net/npm/pretty-print-json@3.0/dist/pretty-print-json.min.js"></script>
    @filamentStyles
    @vite('resources/css/app.css')
</head>
<body class="m-16 bg-gray-300">

<div class="-mt-8 pl-2 mx-auto w-[80%]">
    <label class="font-semibold pr-4">{{ __('rdw-api::form.languageLabel') }}</label>
    <select class="h-18 w-64 p-2 rounded"
            onchange="location.href='/{{ config('rdw-api.rdw_api_folder')  }}/{{ config('rdw-api.rdw_api_demo_slug') }}/change-language/'+this.value">
        <option value="">Select</option>
        <option value="nl" {{ ($language=='nl'?'selected':'') }}>Nederlands</option>
        <option value="en" {{ ($language=='en'?'selected':'') }}>English</option>
    </select>
</div>
<div class="w-full bg-gray-300">
    <div class="mx-auto pt-12 w-[80%] max-w[600px]">
        <div class="flex flex-wrap tabs">
            <input type="radio" name="tabs" id="tab1" checked
                   value="1"
                   class="hidden [&:checked+label]:bg-white [&:checked+label+div]:block">
            <label for="tab1"
                   class="order-1 px-4 py-4 mr-1 rounded rounded-b-none cursor-pointer font-semibold bg-gray-200 transition">
                Post Full Form<span class="block text-center text-sm font-normal">Laravel</span>
            </label>
            <div class="order-last p-4 bg-white w-full rounded rounded-tl-none transition">
                <form method="POST" action="/{{ config('rdw-api.rdw_api_folder') }}/{{ config('rdw-api.rdw_api_demo_slug') }}/{{ $language }}?tab=1"
                      class="flex flex-col items-stretch gap-4 mx-auto w-[90%] mb-8 align-middle justify-center">
                    @csrf
                    <fieldset class="border rounded-lg p-8 mt-8">
                        <div>
                            <input type="text"
                                   class="bg-yellow-500 w-full text-[46px] tracking-widest uppercase space-x-2 font-bold h-16 border-2  border-black border-l-blue-800 border-l-[52px] rounded"
                                   name="licenseplate"
                                   minlength="6"
                                   maxlength="8"
                                   required
                                   value="{{ $licenseplate ?? '' }}"/>
                        </div>
                        <div class="py-4">
                            <label
                                class="block font-semibold pl-1 pb-1.5">{{ __('rdw-api::form.selectdatasetLabel') }}</label>
                            <select name="endpoints[]"
                                    id="endpoints"
                                    style="background-image: none"
                                    class="border rounded h-40 py-3 px-3 w-full"
                                    required
                                    multiple>
                                @foreach(Jdkweb\Rdw\Enums\Endpoints::cases() as $type)
                                    <option
                                        value="{{ $type->value }}" {{ (in_array($type->value, $endpoints) ? 'selected':'') }}>
                                        {{ $type->getLabel() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="pb-4">
                            <input type="checkbox"
                                   {{ ($allEndpoints ? 'checked':'') }}
                                   onclick="[].forEach.call(document.getElementById('endpoints').options,(i)=>{i.selected=this.checked})"
                                   class="w-4 mr-4"
                                   name="allEndpoints"
                                   value="1"/><span class="text-lg">{{ __('rdw-api::form.selectallLabel') }}</span>
                        </div>
                        <div>
                            <label class="block font-semibold pl-1 pb-1.5">{{ __('rdw-api::form.formatLabel') }}</label>
                            <select name="outputformat"
                                    class="border rounded w-full h-12 px-4"
                                    required>
                                @foreach(Jdkweb\Rdw\Enums\OutputFormat::cases() as $format)
                                    <option value="{{ $format->name }}" {{ ($outputformat == $format->name ? 'selected' : '' ) }} >
                                        {{ $format->getLabel()  }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </fieldset>
                    <div class="">
                        <button type="submit" class="float-left mt-4 px-5 py-3 bg-blue-500 text-white rounded">
                            Submit
                        </button>
                    </div>
                </form>
            </div>
            @if($filamentInstalled)
            <input type="radio" name="tabs" id="tab2"
                   value="2"
                   class="hidden [&:checked+label]:bg-white [&:checked+label+div]:block">
            <label for="tab2" onclick="location.href='/{{ config('rdw-api.rdw_api_folder') }}/{{ config('rdw-api.rdw_api_filament_folder') }}/{{ config('rdw-api.rdw_api_demo_slug') }}'"
                   class="order-1 px-4 py-4 mr-1 rounded rounded-b-none cursor-pointer font-semibold bg-gray-200 transition">
                Filament Forms<span class="block text-center text-sm font-normal">Laravel/Filament</span>
            </label>
            @endif
            <div class="order-last p-4 bg-white w-full rounded rounded-tl-none transition">
            </div>
        </div>
        @if(!empty($results))
            @if($outputformat == 'JSON')
                <x-json  :results="$results" />
            @elseif($outputformat == 'XML')
                <x-xml  :results="$results" />
            @elseif($outputformat == 'ARRAY')
                <x-array :results="$results" />
            @endif
        @endif
    </div>
</div>
@vite('resources/js/app.js')
</body>
</html>
