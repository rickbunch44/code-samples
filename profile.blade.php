<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('inc.head')

    <style>
        body {
            background-color: #EAEAEA;
            font-family: 'Nunito';
        }

        .divider-line {
            width: 100%;
            height: 1px;
            border-top: 1px solid #CCC;
            margin-top: 60px;
        }

        .page-options {
            position: absolute;
            top: -30px;
            left: 0;
        }

        .page-options-right {
            right: 0;
            margin-right: 8.33333333%;
            left: inherit;
        }

        .page-options-right .page-option-dropdown {
            right: 5px;
            left: inherit;
        }

        .page-options-right .page-option:hover ~ .page-option-dropdown,
        .page-options-right .page-option-dropdown:hover {
            border-radius: 4px 0 4px 4px;
        }

        .page-option-container {
            display: inline-block;
            position: relative;

        }

        .page-option-dropdown {
            position: relative;
            top: 36px;
            left: 0;
            min-height: 50px;
            width: auto;
            min-width: 130px;
            background-color: rgba(0, 0, 0, .8);
        }

        .page-option-dropdown {
            padding: 7px 15px 7px 12px;
        }

        .page-options-right .page-option-dropdown ul {
            text-align: right;
        }

        .page-option-dropdown ul {
            list-style: none;
            padding: 5px;
            border-radius: 4px;
            margin: 0;
        }

        .page-option-dropdown ul li {
            color: white;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .page-option-dropdown ul li:hover {
            color: #ff940d;
        }

        .page-option-dropdown ul li:last-of-type {
            margin-bottom: 2px;
        }

        .page-option {
            margin: 0;
            background-color: rgba(0, 0, 0, .72);
            border-radius: 4px;
            width: auto;
            color: white;
            padding: 7px 12px;
            margin-right: 5px;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
        }

        .page-option:hover {
            background-color: #1894c5;
        }

        .big-menu-icon-container {
            /*background:transparent;*/
            border-radius: 50%;
            padding: 7px;
            box-shadow: 0 0 2px #605f5f;
        }

        .big-menu-icon-container:hover {
            border-radius: 50%;
        }

        .selected-option {
            color: #919191 !important
        }

        .big-menu-icon {
            transform: rotate(90deg);
        }

        .page-option-dropdown {
            display: none;
            cursor: pointer;
        }

        .page-option-dropdown:hover {
            display: block;
            position: absolute;
            border-radius: 0 4px 4px 4px;
        }

        .page-option:hover ~ .page-option-dropdown {
            display: block;
            position: absolute;
            border-radius: 0 4px 4px 4px;
        }

        .page-option-container:hover .page-option {
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
        }

        .main-profile-pic {
            width: 160px;
            height: 160px;
            border-radius: 50%;
            display: block;
            margin: 40px auto 0;
            box-shadow: 0 0 2px #111;
        }

        .main-profile-name {
            font-family: 'Nunito';
            font-size: 30px;
            font-weight: bold;
            color: #333;
            margin: 15px 0 0;
            text-align: center;
        }

        .main-profile-title {
            font-family: 'Nunito';
            font-size: 17px;
            color: #555;
            margin: 2px 0 10px 0;
            text-align: center;
            font-weight: bold;
            font-style: italic;
        }

        .main-profile-bio {
            text-align: center;
            max-width: 80%;
            min-width: 200px;
            width: 400px;
            max-height: 300px;
            display: block;
            margin: 0 auto;
            color: #444;
            font-weight: bold;
        }

        .main-profile-contact-area {
            margin-top: 0;
        }

        .main-profile-contact-section {
            display: block;
            margin: 30px auto;
            padding-top: 15px;
            width: 100%;
        }

        .main-profile-contact-box {
            margin: 0 0 20px 15%;
        }

        .main-profile-contact-box .contact-pic {
            border-radius: 3px;
            padding: 1px;
            display: inline-block;
            vertical-align: bottom;
        }

        .main-profile-contact-box .contact-title {
            display: inline-block;
            font-size: 18px;
            vertical-align: middle;
            margin-top: 3px;
            margin-left: 30px;
            font-weight: bold;
        }

        .main-profile-social-area {
            margin-top: 0;
        }

        .main-profile-social-area:first-of-type {
            border-top: 45px solid #3085ff;
        }

        .main-profile-social-section {
            margin: 30px auto;
            padding-top: 15px;
            width: 100%;
        }

        .main-profile-social-box {
            display: inline-block;
            width: 100%;
            margin-bottom: 20px;
        }

        .main-profile-social-box .social-pic {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: block;
            vertical-align: super;
            margin: 0 auto 8px;
        }

        .main-profile-social-box .social-title {
            display: block;
            font-size: 18px;
            vertical-align: text-bottom;
            text-align: center;
            font-weight: bold;
        }

        #user-listings {
            background-color: #E3E3E3;
            box-shadow: 0 0 2px #838383;
            padding: 15px;
        }

        .user-container {
            cursor: pointer;
            padding: 12px 20px;
            border-bottom: 1px solid #CCC;
            background-color: #EAEAEA;
            width: 100%;
        }

        .user-container:nth-of-type(odd) {
            background-color: #EFEFEF;
        }

        .user-container:hover {
            background-color: #EEE;
        }

        .user-profile-image {
            vertical-align: middle;
            vertical-align: -webkit-baseline-middle;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 0 1px #CCC;
        }

        .user-profile-name {
            margin-left: 15px;
            font-family: 'Nunito', sans-serif;
            display: inline-block;
            font-size: 20px;
            font-weight: bold;
            color: #444;
        }

        .user-profile-title {
            margin-left: 69px;
            margin-top: -24px;
            margin-bottom: 0;
            font-size: 16px;
            color: #444;
            font-style: italic;
        }

        .user-send-button {
            position: absolute;
            top: 15px;
            right: 15px;
            background-color: #1894c5;
            padding: 4px 6px;
            color: white;
            font-size: 12px;
            font-weight: bold;
            outline: none;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        .user-send-button:hover {
            background-color: #1883c2;
        }

        .user-send-button:focus {
            outline: none;
        }

        .user-send-button img {
            filter: invert(100%);
            height: 16px;
            width: 16px;
            margin-left: 3px;
            margin-bottom: 1px;
        }

        .slide-fade-enter-active {
            transition: all .7s ease;
        }

        .slide-fade-leave-active {
            transition: all .8s cubic-bezier(1.0, 0.5, 0.8, 1.0);
        }

        .slide-fade-enter, .slide-fade-leave-to
            /* .slide-fade-leave-active below version 2.1.8 */
        {
            transform: translateX(30px);
            opacity: 0;
        }

        @media screen AND (max-width: 768px) {
            .ping-modal {
                top: 35%;
            }

            .main-profile-contact-box {
                margin: 0 auto 20px;
                display: block;
                width: 70%;
            }

            .main-profile-social-box {
                margin-bottom: 45px;
            }

            .page-options {
                margin-top: -15px;
            }
        }

        @media screen AND (max-width: 480px) {
            .ping-modal {
                top: 50%;
            }

            .main-profile-contact-box {
                margin: 0 auto 20px;
                display: block;
                width: 80%;
                text-align: center;
            }

            .main-profile-contact-box:last-of-type {
                margin-bottom: 0;
            }

            .main-profile-contact-box .contact-pic {
                display: none;
            }

            .main-profile-contact-box .contact-title {
                margin-left:0;
            }

            .main-profile-social-box {
                margin-bottom: 30px;
            }

            #user-listings {
                margin: -5px;
                padding: 0;
            }

            .user-container .user-profile-name {
                margin-left: 0;
                margin-bottom: 20px;
                font-size: 17px;
            }

            .user-container .user-profile-title {
                margin-left: 0;
                font-size: 14px;
            }

            .user-container .user-profile-image {
                display: none;
                margin-bottom: 8px;
            }

            .user-send-button {
                top: 10px;
                right: 15px;
                padding: 2px 4px;
            }
        }
    </style>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
</head>
<body>
<div class="flex-center position-ref full-height">
    <div id="app" class="content">
        @include('inc.header')

        <div id="page-container">
            <div v-cloak v-if="modal.open" @click="closeModal()" class="dark-backdrop"></div>
            <div id="notify-popup">
                <h5 class="notify-message"></h5>
            </div>
            {{--<img v-cloak--}}
            {{--v-if="membersModal.loading && membersModal.groupId"--}}
            {{--src="http://pingtheworld.us/bundles/app/img/icons/ping-logo-animation.gif" class="ping-loading-icon"/>--}}
            <div v-cloak v-if="modal.open" class="ping-modal-container relative">
                <div class="ping-modal">
                    <div v-if="modal.sending">
                        <h4 class="heading">
                            Send To
                        </h4>
                        <div id="user-listings">
                            <transition name="slide-fade">
                                <div v-cloak v-if="connections" class="row col-xs-12">
                                    <div v-if="!connections || connections.count == 0" class="user-container relative">
                                        <p class="empty center-align" style="margin-top:25px;">No connections to
                                            show.</p>
                                    </div>
                                    <div v-if="connections"
                                         v-for="connection in connections"
                                         class="user-container relative">
                                        <img class="user-profile-image" :src="connection.profile_pic"/>
                                        <p v-text="connection.fname + ' ' + connection.lname"
                                           class="user-profile-name"></p>
                                        <p v-text="connection.title + ', ' + connection.company"
                                           class="user-profile-title"></p>
                                        <button @click="sendConnectionRequest(ownProfile ? 'send' : 'request')"
                                                class="user-send-button">
                                            <img src="https://www.flaticon.com/premium-icon/icons/svg/640/640757.svg"
                                                 height="20" width="20"/>
                                        </button>
                                        {{--<button class="user-send-button">Send</button>--}}
                                    </div>
                                </div>
                            </transition>
                        </div>
                    </div>
                </div>
            </div>
            <div v-cloak v-if="user && currentCard" class="profile-container relative">
                <div v-if="user.cards.length > 1" class="page-options page-options-left offset-1">
                    <div class="page-option-container">
                        <p v-cloak v-if="currentCard" v-text="currentCard.name + ' &#11206;'" class="page-option"></p>
                        <div class="page-option-dropdown">
                            <ul>
                                <li @click="switchCard(index)" v-for="(card, index) in user.cards" v-text="card.name"
                                    :class="{'selected-option': card.uuid === currentCard.uuid}"></li>
                            </ul>
                        </div>
                    </div>
                    {{--<div class="page-option-container">--}}
                    {{--<p v-cloak v-if="currentCard && user.cards.length > 1 && ownProfile" class="page-option">Edit Cards--}}
                    {{--</p>--}}
                    {{--</div>--}}

                    {{--<div class="page-option"></div>--}}
                </div>
                <div class="page-options page-options-right offset-1">
                    <div class="page-option-container">
                        <p v-cloak class="page-option">Options &#11206;</p>
                        {{--<div class="big-menu-icon-container page-option">--}}
                        {{--<img src="http://pingtheworld.us/bundles/app/img/icons/dot-menu.png" class="big-menu-icon"--}}
                        {{--height="30" width="30"/>--}}
                        {{----}}
                        {{--</div>--}}
                        <div class="page-option-dropdown">
                            <ul>
                                <li v-cloak v-if="!ownProfile"
                                    @click="connected ? sendDisconnectionRequest() : sendConnectionRequest('request')"
                                    v-text="connected ? 'Disconnect' : 'Connect'"
                                    :class="{'selected-option': processing.blocking || processing.connecting}"></li>
                                {{--<li v-cloak v-if="ownProfile" @click="shareProfile()" v-text="'Share'"></li>--}}
                                <li v-cloak v-if="ownProfile" @click="openModal('send')" v-text="'Send To'"></li>
                                <li v-cloak v-if="ownProfile" @click="openModal('edit')" v-text="'Edit Card'"></li>
                                <li v-cloak v-if="ownProfile" @click="openModal('settings')" v-text="'Settings'"></li>
                                <li v-cloak v-if="!ownProfile" @click="blockUser()" v-text="'Block'"
                                    :class="{'selected-option': processing.blocking || processing.connecting}"></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="main-profile-area">
                    <img class="main-profile-pic" :src="currentCard.card_settings.profile_pic" width="125"
                         height="125"/>
                    <h3 v-html="user.details.fname + ' ' + user.details.lname" class="main-profile-name"></h3>
                    <h3 v-cloak v-if="currentCard.card_settings.company && currentCard.card_settings.title"
                        v-html="currentCard.card_settings.title + ', ' + currentCard.card_settings.company"
                        class="main-profile-title"></h3>
                    <p v-html="currentCard.card_settings.bio" class="main-profile-bio"></p>
                </div>

                <div class="divider-line"></div>
                <div class="row">
                    <div class="col-xs-12 col-lg-5 offset-lg-1 main-profile-contact-area">
                        <div class="main-profile-contact-section">
                            <div v-cloak v-if="currentCard.card_settings.phone" class="main-profile-contact-box">
                                <img class="contact-pic" src="/img/icons/phone_icon.png" width="30" height="30"/>
                                <h3 v-text="prettyPhone(currentCard.card_settings.phone)" class="contact-title"></h3>
                            </div>
                            <div v-cloak v-if="currentCard.card_settings.email" class="main-profile-contact-box">
                                <img class="contact-pic" src="/img/icons/email_icon.png" width="30" height="30"/>
                                <h3 v-text="currentCard.card_settings.email" class="contact-title"></h3>
                            </div>
                            <div v-cloak v-if="currentCard.card_settings.address" class="main-profile-contact-box">
                                <img class="contact-pic" src="/img/icons/home_icon.png" width="30" height="30"/>
                                <h3 v-text="currentCard.card_settings.address" class="contact-title"></h3>
                            </div>
                            <div v-cloak v-if="currentCard.card_settings.website" class="main-profile-contact-box">
                                <img class="contact-pic" src="/img/icons/website_icon.png" width="30" height="30"/>
                                <h3 v-text="currentCard.card_settings.website" class="contact-title"></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-lg-5 main-profile-social-area">
                        <div class="row col-sm-12 offset-2 main-profile-social-section">
                            <div v-cloak v-if="currentCard.card_settings.facebook"
                                 class="col-6 col-sm-6 col-md-4 col-lg-4 main-profile-social-box">
                                <img class="social-pic" src="http://pingtheworld.us/bundles/app/img/facebook-icon.png"
                                     width="50" height="50"/>
                                <h3 v-text="currentCard.card_settings.facebook.handle" class="social-title"></h3>
                            </div>
                            <div v-cloak v-if="currentCard.card_settings.instagram"
                                 class="col-6 col-sm-6 col-md-4 col-lg-4 main-profile-social-box">
                                <img class="social-pic" src="http://pingtheworld.us/bundles/app/img/instagram-icon.png"
                                     width="50" height="50"/>
                                <h3 v-text="currentCard.card_settings.instagram.handle" class="social-title"></h3>
                            </div>
                            <div v-cloak v-if="currentCard.card_settings.snapchat"
                                 class="col-6 col-sm-6 col-md-4 col-lg-4 main-profile-social-box">
                                <img class="social-pic" src="http://pingtheworld.us/bundles/app/img/snapchat-icon.png"
                                     width="50" height="50"/>
                                <h3 v-text="currentCard.card_settings.snapchat.handle" class="social-title"></h3>
                            </div>

                            <div v-cloak v-if="currentCard.card_settings.twitter"
                                 class="col-6 col-sm-6 col-md-4 col-lg-4 main-profile-social-box">
                                <img class="social-pic" src="http://pingtheworld.us/bundles/app/img/twitter-icon.png"
                                     width="50" height="50"/>
                                <h3 v-text="currentCard.card_settings.twitter.handle" class="social-title"></h3>
                            </div>
                            <div v-cloak v-if="currentCard.card_settings.linkedin"
                                 class="col-6 col-sm-6 col-md-4 col-lg-4 main-profile-social-box">
                                <img class="social-pic" src="http://pingtheworld.us/bundles/app/img/linkedin-icon.png"
                                     width="50" height="50"/>
                                <h3 v-text="currentCard.card_settings.linkedin.handle" class="social-title"></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.6.10/vue.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.0/axios.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script type="text/javascript" src="{{ asset('js/dependencies.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/master.js') }}"></script>

<script>
    let ext = window;
    const app = new Vue({
            el: '#app',
            mounted: () => {
                Vue.nextTick(() => {
                    app.getUserCoordinates();
                    Vue.set(app, "user", JSON.parse(app.userRaw.replace(/(&quot\;)/g, "\"")));
                    Vue.set(app, "currentCard", app.user.cards[0]);
                    Vue.set(app, "ownProfile", parseInt('{{ json_encode($ownProfile) }}') == true ? true : false);
                });
            },
            data: () => {
                return {
                    userRaw: `{{$user}}`,
                    user: null,
                    userLocation: {
                        latitude: null,
                        longitude: null,
                    },
                    currentCard: null,
                    ownProfile: false,
                    connected: false,
                    connections: null,
                    processing: {
                        connecting: false,
                        blocking: false,
                    },
                    modal: {
                        open: false,
                        sending: false,
                        blocking: false,
                        settings: false,
                    },
                    filterBy: {
                        favorites: false,
                        company: false,
                        alpha_a: false,
                        alpha_z: false,
                    },
                    pagination: {
                        query: null,
                        page: 1,
                        totalUsers: null,
                        totalPages: null,
                        size: 4,
                    },
                    showNotifier: false,
                }
            },
            methods: {
                loadConnections: () => {
                    axios.post('{{ route('load_users') }}',
                        {
                            connectionsOnly: false,
                            filterBy: app.filterBy,
                            pagination: app.pagination,
                            // requestingUser: '',
                        }).then(function (response) {
                        Vue.set(app, "connections", response.data.users);
                        // Vue.set(app.pagination, "totalUsers", result.data.totalUsers);
                        // Vue.set(app.pagination, "totalPages", result.data.totalPages);
                        Vue.nextTick(function () {
                            app.connections.forEach(function (user, index) {
                                Vue.set(app.connections[index], "showMenu", false);
                            });
                        });
                    }).catch(function () {
                        alert('failure!');
                    }).then(function () {
                    });
                },
                getNearbyUsers: () => {
                    Vue.nextTick(() => {
                        console.log(app.userLocation.latitude);
                        axios.post('{{ route("get_nearby_users") }}', {
                            latitude: app.userLocation.latitude,
                            longitude: app.userLocation.longitude,
                            radius: 15,
                        }).then((response) => {
                            app.notify("Location obtained.");
                        }).catch(() => {
                            app.notify("Location request failed.");
                        }).then(() => {
                        });
                    });
                },
                getUserCoordinates: () => {
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition((position) => {
                            Vue.set(app.userLocation, "latitude", position.coords.latitude);
                            Vue.set(app.userLocation, "longitude", position.coords.longitude);
                            // return {
                            //     latitude: self.userLocation.latitude,
                            //     longitude: self.userLocation.longitude,
                            // };
                        });
                    } else {
                        alert('failed to get location');
                    }
                },
                sendConnectionRequest:
                    (action) => {
                        if (action !== 'send' && action !== 'request') {
                            return;
                        }

                        Vue.set(app.processing, "connecting", true);
                        let userId = app.user.details.uuid;

                        axios.post('{{ route("connection_request") }}', {
                            userId: userId,
                            cardId: app.currentCard.uuid,
                            action: action,
                        }).then((response) => {
                            Vue.set(app, "connected", true);
                            app.notify("Request sent.");
                        }).catch(() => {
                            Vue.set(app.processing, "connecting", false);
                            app.notify("Request already sent.");
                        }).then(() => {
                            Vue.set(app.processing, "connecting", false);
                        });
                    },
                sendDisconnectionRequest:
                    () => {
                        Vue.set(app.processing, "connecting", true);

                        axios.post('{{ route("disconnection_request") }}', {
                            cardId: app.currentCard.uuid
                        }).then((response) => {
                            Vue.set(app, "connected", false);
                            console.log(response.data);
                        }).catch(() => {
                        }).then(() => {
                            Vue.set(app.processing, "connecting", false);
                        });
                    },
                openModal:
                    (action) => {
                        Vue.set(app.modal, "open", true);

                        switch (action) {
                            case 'send': {
                                Vue.set(app.modal, "sending", true);
                            }
                        }
                    },
                closeModal:
                    () => {
                        Vue.set(app.modal, "open", false);
                    },
                switchCard:
                    (index) => {
                        Vue.set(app, "currentCard", app.user.cards[index]);
                        Vue.set(app.user.cards[index], "selected", true);
                    },
                prettyPhone:
                    (str) => {
                        return str.replace(/\D+/g, '')
                            .replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3');
                    },
                notify:
                    (msg) => {
                        let notice = $("#notify-popup");
                        app.closeModal();
                        notice.find(".notify-message").html(msg);
                        notice.fadeIn(500);


                        setTimeout(function () {
                            notice.fadeOut(500);
                        }, 3000);
                    }
            },
            watch: {
                "modal.sending":
                    (sending) => {
                        if (sending) {
                            app.loadConnections();
                        }
                    },
                "userLocation.latitude":
                    (coords) => {
                        if (coords) {
                            app.getNearbyUsers();
                        }
                    },
            }
        })
    ;
</script>
</body>
</html>
