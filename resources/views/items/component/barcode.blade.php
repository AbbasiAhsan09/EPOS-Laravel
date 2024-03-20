<div style="width: fit-content; margin : auto; max-width : 100%">
    @isset($code)
@php
    // $generator = new Picqer\Barcode\BarcodeGeneratorHTML();
    $generatorPNG = new Picqer\Barcode\BarcodeGeneratorPNG();

@endphp
<img src="data:image/png;base64,{{ base64_encode($generatorPNG->getBarcode($code, $generatorPNG::TYPE_CODE_128)) }}" width="300px" height="50px">
{{-- {!! $generator->getBarcode($code, $generator::TYPE_CODE_128) !!} --}}
<p><small>{{$code}}</small></p>
@endisset
</div>
  
  
{{-- <h3>Product 2: 000005263635</h3>
@php
    $generatorPNG = new Picqer\Barcode\BarcodeGeneratorPNG();
@endphp
  
 --}}
