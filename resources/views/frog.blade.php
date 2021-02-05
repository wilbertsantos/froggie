<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Froggie') }}
        </h2>
    </x-slot>

<script type="text/javascript">
function convertToCSV(objArray) {
    var array = typeof objArray != 'object' ? JSON.parse(objArray) : objArray;
    var str = '';

    for (var i = 0; i < array.length; i++) {
        var line = '';
        for (var index in array[i]) {
            if (line != '') line += ','

            line += array[i][index];
        }

        str += line + '\r\n';
    }

    return str;
}

function exportCSVFile(headers, items, fileTitle) {
    if (headers) {
        items.unshift(headers);
    }

    // Convert Object to JSON
    var jsonObject = JSON.stringify(items);

    var csv = this.convertToCSV(jsonObject);

    var exportedFilenmae = fileTitle + '.csv' || 'export.csv';

    var blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    if (navigator.msSaveBlob) { // IE 10+
        navigator.msSaveBlob(blob, exportedFilenmae);
    } else {
        var link = document.createElement("a");
        if (link.download !== undefined) { // feature detection
            // Browsers that support HTML5 download attribute
            var url = URL.createObjectURL(blob);
            link.setAttribute("href", url);
            link.setAttribute("download", exportedFilenmae);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    }
}
</script>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    Speckled Frog!
                </div>

                <div class="py-12 grid-cols-12 m-5">

                    <form method="POST" action="{{ route('frog') }}">
                        @csrf
                        <!-- Validation Errors -->
                        <x-auth-validation-errors class="mb-4" :errors="$errors" />
                        <!-- Email Address -->
                        <div>
                            <x-label for="urls" :value="__('URLS')" />

                            <x-textarea id="urls" class="block mt-1 w-full" name="urls" :value="old('urls')" required autofocus />

                            <x-button class="float-right mt-5 mb-5" type="submit">
                                {{ __('Test') }}
                            </x-button>
                        </div>
                    </form>
                </div>

                @if (@$summary)
                    <div class="clear-both">
                        <h3 class="text-gray-500 uppercase text-2xl mt-5 ml-10">Summary</h3>
                    </div>

                    <div class="flex justify-around mx-8 px8 mt-10 mb-10">
                        <div class="bg-blue-100 rounded-md p-8 uppercase">
                            <p class="leading-3">TOTAL REQUEST: <span class="font-bold text-2xl ">{{$summary['totalRequest']}}</span></p>
                        </div>
                        <div class="bg-green-100 rounded-md p-8 uppercase">
                            <p class="leading-3">TOTAL PASSED: <span class="font-bold text-2xl ">{{$summary['totalPassed']}}</span></p>
                        </div>
                        <div class="bg-red-100 rounded-md p-8 uppercase">
                            <p class="leading-3">TOTAL FAILED: <span class="font-bold text-2xl ">{{$summary['totalFailed']}}</span></p>
                        </div>
                    </div>
                    <script type="text/javascript">
                        var headers = {
                            requestUrl: "Request URL", 
                            statusCode: "Status Code",
                            redirectUrl: "Redirect URL",
                            rdrStatusCode: "Status Code",
                            flag: "FLAG"
                        };
                        var itemsNotFormatted = [];
                    </script>
                @endif
                @if (@$results)
                    <!-- This example requires Tailwind CSS v2.0+ -->
                    <div class="flex flex-col px-8 mx-8 mb-8">
                      <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                          <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                              <thead class="bg-gray-50">
                                <tr>
                                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Request URL
                                  </th>
                                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status Code
                                  </th>
                                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Redirect URL
                                  </th>
                                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status Code
                                  </th>
                                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    FLAG
                                  </th>
                                </tr>
                              </thead>
                              <tbody class="bg-white divide-y divide-gray-200">

                                @foreach($results as $key => $item)

                                <script type="text/javascript">
                                    itemsNotFormatted.push(
                                        {requestUrl: "{{$item['request_url']}}", 
                                        statusCode: "{{$item['status_code']}}",
                                        redirectUrl: "{{$item['redirect_url']}}",
                                        rdrStatusCode: "{{(isset($item['rdr']['status_code'])) ? $item['rdr']['status_code'] : ''}}",
                                        flag: "{{$item['flag']}}"}
                                    );
                                </script>
                        
                                    <tr>
                                      <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            <a href="{{$item['request_url']}}" target="_blank">
                                                {{ Illuminate\Support\Str::limit($item['request_url'], $limit = 50, $end = '...')  }}
                                            </a>
                                        </div>
                                      </td>
                                      <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($item['status_code'] == 200) bg-green-100 text-green-800 
                                        @elseif($item['status_code'] == 301) bg-yellow-100 text-yellow-800 
                                        @else bg-red-100 text-red-800 @endif
                                        ">
                                          {{$item['status_code']}}
                                        </span>
                                      </td>
                                      @if( in_array($item['status_code'],[301,302]))

                                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <a href="{{$item['redirect_url']}}" target="_blank">
                                                {{ Illuminate\Support\Str::limit($item['redirect_url'], $limit = 50, $end = '...')  }}
                                            </a>
                                      </td>

                                      <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($item['rdr']['status_code'] == 200) bg-green-100 text-green-800 
                                        @elseif($item['rdr']['status_code'] == 301) bg-yellow-100 text-yellow-800 
                                        @else bg-red-100 text-red-800 @endif
                                        ">
                                          {{$item['rdr']['status_code']}}
                                        </span>
                                      </td>


                                      @else
                                          <td class="px-6 py-4 whitespace-nowrap">-</td>
                                          <td class="px-6 py-4 whitespace-nowrap">-</td>
                                      @endif

                                      <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if( $item['flag']) bg-green-100 text-green-800 
                                        @else bg-red-100 text-red-800 @endif
                                        ">
                                          @if( $item['flag'])
                                            PASSED
                                          @else 
                                            FAILED
                                          @endif
                                        </span>
                                      </td>


                                    </tr>
                                @endforeach

                                <!-- More items... -->
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="flex justify-around mx-8 px8 mt-10 mb-10">
                        <x-button onclick="exportCSVFile(headers, itemsNotFormatted, 'Speckled-Frog-{{time()}}');">download as csv</x-button>
                    </div>
                @endif


            </div>
        </div>
    </div>

</x-app-layout>
