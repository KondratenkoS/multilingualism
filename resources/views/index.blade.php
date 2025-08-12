@extends('layouts')

@section('content')
<!-- body -->
<div class="bloc l-bloc none full-width-bloc" id="body">
    <div class="container bloc-no-padding-lg">
        <div class="row lt-wrap">
            <div class="col-12 col-lg-6 d-flex">
                <div class=" ">
                    <div id="parallax-background" class="featured-background"
                         style="transform: translate3d(-50%, 0px, 0px);"></div>

                    <style>
                        /* Default Style: LG and above */
                        #parallax-background {
                            position: fixed; /* Ensure parallax effect */
                            top: -2vh;
                            left: 55%;
                            transform: translateX(-50%);
                            width: 120%;
                            height: 160vh;
                            background-image: url('http://shulgin.care.myshelter.synology.me/wp-content/uploads/2024/10/irina-shulgin-welcome-LG.jpg?ver=1734432446');
                            background-size: cover;
                            background-position: center calc(50% - 0.5vh); /* Align center and top */
                            z-index: -1;
                            will-change: transform;
                            pointer-events: none;
                        }

                        /* MD and below: Use the small image */
                        @media (max-width: 992px) {
                            #parallax-background {
                                background-image: url('http://shulgin.care.myshelter.synology.me/wp-content/uploads/2024/10/irina-shulgin-welcome-XS.jpg?ver=1734432468');
                                width: 140%;
                                background-position: center calc(50% - 0.5vh);
                            }
                        }

                        /* SM: Small Screens (577px - 768px) */
                        @media (min-width: 577px) and (max-width: 768px) {
                            #parallax-background {
                                left: 50%;
                                transform: translateX(-50%);
                                width: 145%;
                                background-position: center calc(50% - 2vh);
                            }
                        }

                        /* XS: Extra Small Screens (≤ 576px) */
                        @media (max-width: 576px) {
                            #parallax-background {
                                left: 50%;
                                transform: translateX(-50%);
                                width: 150%;
                                background-position: center calc(50% - 4vh);
                            }
                        }
                    </style>

                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const parallaxBackground = document.getElementById('parallax-background');

                            if (parallaxBackground) {
                                document.addEventListener('scroll', function () {
                                    const scrollOffset = window.pageYOffset;
                                    const screenWidth = window.innerWidth;
                                    const windowHeight = window.innerHeight;
                                    const documentHeight = document.documentElement.scrollHeight;

                                    // Define multipliers for different breakpoints
                                    let multiplier = -0.2; // Default for LG (≥ 992px)

                                    if (screenWidth <= 992 && screenWidth > 768) {
                                        multiplier = -0.33; // For MD (769px - 992px)
                                    } else if (screenWidth <= 768 && screenWidth > 576) {
                                        multiplier = -0.33; // For SM (577px - 768px)
                                    } else if (screenWidth <= 576) {
                                        multiplier = -0.3; // For XS (≤ 576px)
                                    }

                                    // Calculate max scroll position before stopping
                                    const maxScroll = documentHeight - windowHeight;
                                    const maxParallaxOffset = maxScroll * multiplier;

                                    // Ensure the parallax effect stops at the max allowed offset
                                    const parallaxOffset = Math.max(scrollOffset * multiplier, maxParallaxOffset);

                                    // Apply the parallax effect
                                    parallaxBackground.style.transform = `translate3d(-50%, ${parallaxOffset}px, 0)`;
                                });
                            }
                        });
                    </script>

                </div>
            </div>
            <div class="text-md-start col-lg-6">
                <div class="row">
                    <div class="col text-start">
                        <div
                            class=" d-grid lt-welcome-01 bg-body-tertiary bg-opacity-10 lt-bg-blur-32 rounded-5 p-5">
                            <div class="mx-3 mt-2 mb-2">
                                <h2 class="mb-0 mb-4 text-color-dark-welcome-01">
                                    {{--                                        {{ $introPosts[0]->intro_title }}--}}
                                </h2>
                                <h5 class="mb-0 mb-lg-0 text-color-dark-welcome-01">
                                    <p class="h5 mb-3"><em>
                                            {{--                                                {{ strip_tags($introPosts[0]->intro_post) }}--}}
                                        </em></p>
                                    <p class="h5 mb-3"></p>
                                </h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- body END -->

<!-- bloc-3 -->
<div class="bloc l-bloc py-5" id="bloc-3">
    <div class="container bloc-lg bloc-no-padding-lg">
        <div class="row gap-5 d-lg-flex">

            <div class="col">
                <div class="row g-0">
                    <div class="d-grid lt-bg-blur-32 rounded-5 p-5 col-lg-12 gap-5 bg-opacity-75 bg-body">
                        <div class="row d-grid my-4 mx-3">
                            <div class="col-12">
                                <h2 class="mb-lg-2 mb-2">
                                    {{--                                        {{ $introPosts[0]->left_intro_title }}--}}
                                </h2>
                                <p class="mb-0 text-secondary">
                                </p>
                                <p>
                                    {{--                                        {{ strip_tags($introPosts[0]->left_intro_post) }}--}}
                                </p>
                                <p></p>
                            </div>
                        </div>
                        <div class="row mb-2 mx-3">
                            {{--                                @foreach($leftPosts as $leftPost)--}}
                            <div class="d-grid col-12">
                                <div class="row">
                                    <div class="col">
                                        <div class="divider-h divider-background-color">
                                        </div>
                                    </div>
                                </div>
                                <h4 class="mb-lg-2 mb-2">
                                    {{--                                            {{ $leftPost->title }}--}}
                                </h4>
                                <p class="mb-lg-1 mb-1 text-secondary">
                                    {{--                                            {{ strip_tags($leftPost->body) }}--}}
                                </p>
                            </div>
                            {{--                                @endforeach--}}

                        </div>
                        <div class="row">
                            <div class="col text-end">
                                <div
                                    class="d-inline-flex btn rounded-pill lt-p-btn float-lg-none text-lg-start lt-body-bg"
                                    onclick="window.location.href='http://shulgin.care.myshelter.synology.me/%d1%81-%d1%87%d0%b5%d0%bc-%d0%bc%d0%be%d0%b6%d0%bd%d0%be-%d0%be%d0%b1%d1%80%d0%b0%d1%82%d0%b8%d1%82%d1%8c-%d0%ba%d0%be-%d0%bc%d0%bd%d0%b5/';">
                                    <h5 class="mb-lg-0 z-3 mb-0 text-secondary">
                                        Ream more
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col lt-welcome-context">
                <div class="row g-0">
                    <div class="col-lg-12 col-lg-6 d-grid lt-bg-blur-32 rounded-5 p-5 gap-5 bg-opacity-75 bg-body">
                        <div class="row mb-3 mb-lg-3 d-grid my-4 mx-3">
                            <div class="text-start col-lg-12 col-12 col">
                                <h2 class="mb-sm-0 mb-lg-2 mb-2">
                                    {{--                                        {{ $introPosts[0]->right_intro_title }}--}}
                                </h2>
                                <p class="mb-lg-0 mb-0 text-secondary">
                                    {{--                                        {{ strip_tags($introPosts[0]->right_intro_post) }}--}}
                                </p>
                            </div>
                        </div>

                        <div class="row mb-2 mx-3">
                            {{--                                @foreach($rightPosts as $rightPost)--}}
                            <div class="col-lg-12 col-12 ">
                                <div class="row">
                                    <div class="col">
                                        <div class="divider-h divider-background-color">
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-lg-2 mb-2 mt-lg-2 mt-2">
                                    <div class="col d-inline-flex gap-3">
                                        <div class=" " id="icon-svg">
                                            <div class="icon_svg-wrapper"
                                                 style="width: var(--icon-size); height: var(--icon-size);">
                                                <!--?xml version="1.0" encoding="UTF-8"?-->
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                     fill="currentColor" viewBox="0 0 24 24">
                                                    <path fill-rule="evenodd"
                                                          d="m13 13.5.006 3.983c1.88-.069 3.242-.336 4.24-.774 1.124-.492 1.825-1.218 2.296-2.25.492-1.078.746-2.522.863-4.457.1-1.655.099-3.582.096-5.845V4.02c-3.888.164-5.609 1.3-6.461 2.69-.49.799-.76 1.78-.899 2.948C13.001 10.83 13 12.112 13 13.5Zm-1.991 5.973.005 3.529 2-.003-.005-3.515c2.052-.07 3.714-.361 5.041-.943 1.584-.695 2.64-1.782 3.311-3.25.649-1.422.92-3.166 1.04-5.168.105-1.722.103-3.722.1-5.978L22.5 3V2h-1c-4.901 0-7.712 1.295-9.165 3.665a7.699 7.699 0 0 0-.845 1.992C10.089 5.957 7.53 4.5 3 4.5H2v1c0 .34-.004.705-.01 1.087-.01.965-.024 2.04.03 3.104.074 1.5.283 3.09.846 4.545.57 1.473 1.509 2.817 3.028 3.78 1.312.832 2.992 1.339 5.115 1.457Zm-.003-2.004c-1.806-.115-3.102-.546-4.041-1.142-1.093-.693-1.788-1.66-2.234-2.813-.453-1.17-.644-2.517-.714-3.923-.05-.999-.038-1.97-.027-2.912l.002-.152c3.57.2 5.263 1.474 6.09 2.574a4.8 4.8 0 0 1 .82 1.706 4.044 4.044 0 0 1 .11.678.63.63 0 0 1 .002.024c0 .001 0 .002 0 0m0 0v-.005h.003C11 12.157 11 12.816 11 13.465l.006 4.003"
                                                          clip-rule="evenodd"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <h4 class="mb-sm-0 mb-lg-0 mb-0">
                                            {{--                                                    {{ $rightPost->title }}--}}
                                        </h4>
                                    </div>
                                </div>
                                <p class="mb-lg-2 mb-2 mb-sm-0 text-secondary mb-md-2">
                                    {{--                                            {{ strip_tags($rightPost->body) }}--}}
                                </p>
                                <div class="row">
                                    <div class="col text-lg-end text-end">
                                        <div
                                            class="d-inline-flex btn rounded-pill lt-p-btn float-lg-none text-lg-start lt-body-bg"
                                            onclick="window.location.href='http://shulgin.care.myshelter.synology.me/naturopatiya/fitoterapiya/';">
                                            <h5 class="mb-lg-0 z-3 mb-0 text-secondary">
                                                Ream more
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{--                                @endforeach--}}

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="row">
                    <div class="col">
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="row">
                    <div class="col">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- bloc-3 END -->
@endsection
