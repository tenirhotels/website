@extends('layouts.app')

@section('content')
    <main class="page">
        <div class="page-header page-header--medium" data-aos="fade-down" data-aos-duration="700" data-aos-once="true">
            <img src="images/content/head_about.jpg" alt="">
        </div>
        <section class="page-section">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h1 data-aos-duration="700" ata-aos-delay="700" data-aos="fade-right" data-aos-once="true">
                            {{ translate('bronirovanie') }}</h1>
                    </div>
                    <div class="col-12 fz-4" data-aos-duration="700" ata-aos-delay="700"
                         data-aos="fade-left" data-aos-once="true">
                        <style>
                            #tl-anchor{
                                margin-bottom: 20px;
                                text-align: left;
                            }
                            .footer-soc {
                                display: none!important;
                            }
                        </style>
                        <!-- start TL Booking form script -->
			<p id='tl-anchor'>{{translate('booking-first-line')}}<br><br>
                                {{translate('booking-attention-note')}}
			</p>

                        <div id='tl-booking-form'>&nbsp;</div>
                        <script type='text/javascript'>

                            (function(w) {
                                var q = [
                                    ['setContext', 'TL-INT-tenirhotels', '{{Lang::locale()}}'],
                                    ['embed', 'booking-form', {
                                        container: 'tl-booking-form'
                                    }]
                                ];
                                var t = w.travelline = (w.travelline || {}),
                                    ti = t.integration = (t.integration || {});
                                ti.__cq = ti.__cq? ti.__cq.concat(q) : q;
                                if (!ti.__loader) {
                                    ti.__loader = true;
                                    var d = w.document,
                                        p = d.location.protocol,
                                        s = d.createElement('script');
                                    s.type = 'text/javascript';
                                    s.async = true;
                                    s.src = (p == 'https:' ? p : 'http:') + '//ibe.tlintegration.com/integration/loader.js';
                                    (d.getElementsByTagName('head')[0] || d.getElementsByTagName('body')[0]).appendChild(s);
                                }
                            })(window);
                        </script>
                        <!-- end TL Booking form script -->
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
