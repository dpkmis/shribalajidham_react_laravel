
  var videoElement;
  var videoLength;    
async function playerFunction(isArray) {
  // console.log(isArray);
//   console.log(isArray, 'isArray')
  // return false;


  var drm_checker = false;
//   await getSupportedDRMSystems()
//     .then(function(supportedDRM) {
//       if (supportedDRM.length > 0) {
//         console.log('Supported DRM systems:');
//         supportedDRM.forEach(system => {
//           console.log(system);
//           if (system == 'com.microsoft.playready') {
//             console.log('Edge supported DRM');
//             drm_type = "com.microsoft.playready";
//             drm_checker = false;
//             return false;
//           } else if (system == 'com.apple.fps' || system == 'com.apple.fps.1_0') {
//             console.log('Mac supported DRM');
//             drm_checker = true;
//             return false;
//           }

//         });
//       } else {
//         drm_checker = false;
//         drm_type = 'com.widevine.alpha';
//         //   alert('No supported DRM systems found.')
//         console.log('No supported DRM systems found.');
//         return false;
//       }
//     });



  if (drm_checker) {

    var fairplayCertUri = "https://license-global.pallycon.com/ri/fpsKeyManager.do?siteId=ffff";

    function getFairplayCert() {
      var xmlhttp;
      if (window.XMLHttpRequest) {
        xmlhttp = new XMLHttpRequest();
      } else {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
      }
      xmlhttp.open("GET", fairplayCertUri, false);
      xmlhttp.send();

      var fpsCert = shaka.util.Uint8ArrayUtils.fromBase64(xmlhttp.responseText);
      return fpsCert;
    }
  }


   videoElement = document.getElementById(isArray.videoTagId);
  const videoContainer = document.getElementById(isArray.videoTagParentId);
  const playerShaka = new shaka.Player(videoElement);
  window.playerShaka = playerShaka;
  // UI setup

  shaka.polyfill.installAll();

  
  const ui = new shaka.ui.Overlay(playerShaka, videoContainer, videoElement);
  // const controls = ui.getControls();

  // const container = controls.getServerSideAdContainer();
  // const netEngine = playerShaka.getNetworkingEngine();
 
  //const file_url='https://a3686fc261134c65adc3f466220951f5.mediatailor.ap-south-1.amazonaws.com/v1/session/59e28ad99d415e8986bc15766c5585d319844fe3/PRE-ROLL-SKIP/vod_non_drm_ios/3411384/1684578230_6300785840078850/1684578046731_773612204184676000_video_VOD.m3u8'; 
  const file_url = "";
  const HLSmanifestUri = isArray.dashUri;


  // const mediaTailorAdsParams = {
  //   "adsParams": {
  //     "pod_max_duration": "30",
  //     "hobby": "none"
  //   }
  //   /*,
  //               "adSignaling": {
  //                   "enabled": "true"
  //               }*/
  // };



  const defaultConfig = {
    controlPanelElements: [
      "play_pause",
      "time_and_duration",
      "spacer",
      "spacer",
      "volume",
      "mute",
      "overflow_menu",
      "picture_in_picture",
      "fullscreen",
    ],
    overflowMenuButtons: [
      "quality",
      "captions",
      "language",
      "playback_rate"
    

    ],
    enableTooltips: true,
  };

  ui.configure(defaultConfig);
  ui.getControls();





  try {


    if (isArray.token_data) {
    //   var licenseURI = "https://qa.videocrypt.com/index.php/rest_api/v2/course/on_request_create_video_license";
    // licenseURI = 'https://license.videocrypt.com/validateLicense';
    licenseURI = isArray.licenceURL;
      // licenseUri = 'https://testing.videocrypt.com/index.php/rest_api/courses/course/on_request_create_video_license';
      // licenseUri = 'https://license.pallycon.com/ri/licenseManager.do';
      // licenseUri = '<?= 'https://api.videocrypt.com/index.php/rest_api/v2/course/on_request_create_video_license_data'; ?>';




      if (drm_checker) {
       console.log('11111')

        const fairplayCert = getFairplayCert();
        // Configure DRM and license request filters
        playerShaka.configure({
          drm: {
            servers: {

              'com.apple.fps': licenseURI
            },
            advanced: {
              'com.apple.fps': {
                serverCertificate: fairplayCert
              }
            },
          },
          streaming: {
            autoLowLatencyMode: true,
          }
        });


        playerShaka.getNetworkingEngine()
          .registerRequestFilter(function(type, request) {
            if (type == shaka.net.NetworkingEngine.RequestType.LICENSE) {
              const originalPayload = new Uint8Array(request.body);
              const base64Payload = shaka.util.Uint8ArrayUtils.toBase64(originalPayload);
              const params = 'spc=' + encodeURIComponent(base64Payload);

              request.body = shaka.util.StringUtils.toUTF8(params);
              request.headers['Content-Type'] = 'application/x-www-form-urlencoded';
              request.headers["pallycon-customdata-v2"] = isArray.token_data;
            }
          });

        playerShaka.getNetworkingEngine()
          .registerResponseFilter(function(type, response) {
            // Alias some utilities provided by the library.
            if (type == shaka.net.NetworkingEngine.RequestType.LICENSE) {
              const responseText = shaka.util.StringUtils.fromUTF8(response.data)
                .trim();
              response.data = shaka.util.Uint8ArrayUtils.fromBase64(responseText)
                .buffer;
              parsingResponse(response);
            }
          });
      } else {
        let drmConfig = {
          servers: {
            "com.widevine.alpha": licenseURI,
          },
          advanced: {
            "com.widevine.alpha": {
              videoRobustness: "SW_SECURE_DECODE", // Specify video robustness level
              audioRobustness: "SW_SECURE_CRYPTO" // Specify audio robustness level
            }
          }
        };
        playerShaka.configure({
          drm: drmConfig,
          streaming: {
            autoLowLatencyMode: true
          }
        });


        playerShaka.getNetworkingEngine()
          .registerRequestFilter(function(type, request) {
            if (type == shaka.net.NetworkingEngine.RequestType.LICENSE) {
              request.headers[isArray.pallyconToken] = isArray.token_data;
            }
          });


      }
    }



    // if (isArray.isAds) {
    //   var uri = await adManager.requestMediaTailorStream(HLSmanifestUri, mediaTailorAdsParams);

    // } else {
      var uri = HLSmanifestUri;

    // }


    await playerShaka.load(uri);



    ///===============================================================================================
  } catch (error) {
    console.error("Error loading manifest:", error);
  }


}// player function close

