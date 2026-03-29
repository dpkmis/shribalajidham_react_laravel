
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

const controls = ui.getControls();

const container = controls.getServerSideAdContainer();
const netEngine = playerShaka.getNetworkingEngine();
const adManager = playerShaka.getAdManager();

adManager.initMediaTailor(container, netEngine, videoElement);



//const file_url='https://a3686fc261134c65adc3f466220951f5.mediatailor.ap-south-1.amazonaws.com/v1/session/59e28ad99d415e8986bc15766c5585d319844fe3/PRE-ROLL-SKIP/vod_non_drm_ios/3411384/1684578230_6300785840078850/1684578046731_773612204184676000_video_VOD.m3u8'; 
const file_url = "";
const HLSmanifestUri = isArray.dashUri;



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




adManager.addEventListener(shaka.ads.Utils.AD_BREAK_READY, (e) => {
  console.log('AD_BREAK_READY');
});

adManager.addEventListener(shaka.ads.Utils.AD_BUFFERING, (e) => {
  console.log('AD_BUFFERING');
});

adManager.addEventListener(shaka.ads.Utils.AD_CLICKED, (e) => {
  console.log('AD_CLICKED');
});

adManager.addEventListener(shaka.ads.Utils.AD_CLOSED, (e) => {
  console.log('AD_CLOSED');
});

adManager.addEventListener(shaka.ads.Utils.AD_COMPLETE, (e) => {
  console.log('AD_COMPLETE');
});

adManager.addEventListener(shaka.ads.Utils.AD_CONTENT_ATTACH_REQUESTED, (e) => {
  console.log('AD_CONTENT_ATTACH_REQUESTED');
});

adManager.addEventListener(shaka.ads.Utils.AD_CONTENT_PAUSE_REQUESTED, (e) => {
  console.log('AD_CONTENT_PAUSE_REQUESTED');
});

adManager.addEventListener(shaka.ads.Utils.AD_CONTENT_RESUME_REQUESTED, (e) => {
  console.log('AD_CONTENT_RESUME_REQUESTED');
});

adManager.addEventListener(shaka.ads.Utils.AD_DURATION_CHANGED, (e) => {
  console.log('AD_DURATION_CHANGED');
});

adManager.addEventListener(shaka.ads.Utils.AD_ERROR, (e) => {
  console.log('AD_ERROR');
});

adManager.addEventListener(shaka.ads.Utils.AD_FIRST_QUARTILE, (e) => {
  console.log('AD_FIRST_QUARTILE');
});

adManager.addEventListener(shaka.ads.Utils.AD_IMPRESSION, (e) => {
  console.log('AD_IMPRESSION');
});

adManager.addEventListener(shaka.ads.Utils.AD_INTERACTION, (e) => {
  console.log('AD_INTERACTION');
});

adManager.addEventListener(shaka.ads.Utils.AD_LINEAR_CHANGED, (e) => {
  console.log('AD_LINEAR_CHANGED');
});

adManager.addEventListener(shaka.ads.Utils.AD_LOADED, (e) => {
  console.log('AD_LOADED');
  alert('11')

});

adManager.addEventListener(shaka.ads.Utils.AD_METADATA, (e) => {
  console.log('AD_METADATA');
});

adManager.addEventListener(shaka.ads.Utils.AD_MIDPOINT, (e) => {
  console.log('AD_MIDPOINT');
});

adManager.addEventListener(shaka.ads.Utils.AD_MUTED, (e) => {
  console.log('AD_MUTED');
});

adManager.addEventListener(shaka.ads.Utils.AD_PAUSED, (e) => {
  console.log('AD_PAUSED');
});

adManager.addEventListener(shaka.ads.Utils.AD_PROGRESS, (e) => {
  console.log('AD_PROGRESS');
});

adManager.addEventListener(shaka.ads.Utils.AD_RECOVERABLE_ERROR, (e) => {
  console.log('AD_RECOVERABLE_ERROR');
});

adManager.addEventListener(shaka.ads.Utils.AD_RESUMED, (e) => {
  console.log('AD_RESUMED');
});

adManager.addEventListener(shaka.ads.Utils.AD_SKIP_STATE_CHANGED, (e) => {
  console.log('AD_SKIP_STATE_CHANGED');
});

adManager.addEventListener(shaka.ads.Utils.AD_SKIPPED, (e) => {
  console.log('AD_SKIPPED');
});

adManager.addEventListener(shaka.ads.Utils.AD_STARTED, (e) => {
  console.log('AD_STARTED');
  const sdkAdObject = e['sdkAdObject'];
  const originalEvent = e['originalEvent'];
  cosnole.log("SDK OBJECT = " + sdkAdObject);
  cosnole.log("ORIGINAL EVENT = " + originalEvent);
});

adManager.addEventListener(shaka.ads.Utils.AD_STOPPED, (e) => {
  console.log('AD_STOPPED');
});

adManager.addEventListener(shaka.ads.Utils.AD_THIRD_QUARTILE, (e) => {
  console.log('AD_THIRD_QUARTILE');
});

adManager.addEventListener(shaka.ads.Utils.AD_VOLUME_CHANGED, (e) => {
  console.log('AD_VOLUME_CHANGED');
});

adManager.addEventListener(shaka.ads.Utils.ADS_LOADED, (e) => {
  console.log('ADS_LOADED');

});

adManager.addEventListener(shaka.ads.Utils.ALL_ADS_COMPLETED, (e) => {
  console.log('ALL_ADS_COMPLETED');
});

adManager.addEventListener(shaka.ads.Utils.CUEPOINTS_CHANGED, (e) => {
  console.log('CUEPOINTS_CHANGED');


});

adManager.addEventListener(shaka.ads.Utils.AD_RESUMED, (e) => {
    //console.log("AD_RESUMED");
 });

//  adManager.addEventListener(
//     shaka.ads.Utils.AD_SKIP_STATE_CHANGED,
//     (e) => {
//        //console.log("AD_SKIP_STATE_CHANGED",e);
//        let skipCont = document.querySelector('.shaka-skip-ad-container');
//        let skipButton = document.querySelector('.shaka-skip-ad-button');
//        skipCont.classList.add('clickable-btn');
//        skipButton.classList.add('clickable-btn');
//     }
//  );

//  adManager.addEventListener(shaka.ads.Utils.AD_SKIPPED, (e) => {
//     //console.log("AD_SKIPPED");
//     isAddOnPlay = false;
//     isAdPlaying = false;
//     $('.video-ads').css('width',"0%");
//     $(".video_ads_after").css('display','none');
//  });

 adManager.addEventListener(shaka.ads.Utils.AD_STARTED, (e) => {
    //console.log("AD_STARTED");
    $('.pro_gresss').hide();
    $('.shaka-live-button').hide();
    $('.shaka-overflow-menu').addClass('shaka-hidden');
    $('.shaka-settings-menu shaka-resolutions').addClass('shaka-hidden');
    isAddOnPlay = true;
    isAdPlaying = true;
    var adDuration = e.ad.getDuration();
    if(video.playbackRate > 0){
       playbackRate = video.playbackRate;
    }
    var time = video.currentTime;
    video.playbackRate = 1;
    //ui.configure(adConfig);
    $(".shaka-backward-button").addClass("shaka-hidden");
    $(".lockButton").addClass("shaka-hidden");
    $(".shaka-overflow-button").addClass("shaka-hidden");
    $(".shaka-forward-button").addClass("shaka-hidden");
    $(".video_ads_after").css('display','block');
    video.addEventListener("timeupdate", () => {
       if (isAdPlaying) {
          var adseekupdate = Math.ceil((video.currentTime-time)*(100/(adDuration)));
          $('.video-ads').css('width',adseekupdate+"%");
       }
    });  
    const sdkAdObject = e["sdkAdObject"];
    const originalEvent = e["originalEvent"];
 });

 adManager.addEventListener(shaka.ads.Utils.AD_STOPPED, (e) => {
    // console.log("AD_STOPPED");
    $('.pro_gresss').show();
    $('.shaka-live-button').show();
    isAddOnPlay = false;
    video.playbackRate = playbackRate;
    $(".video_ads_after").css('display','none');
    setTimeout(function() {
          $('.pro_gresss').hide();
    }, 3500)
    // ui.configure(defaultConfig);
    $(".shaka-backward-button").removeClass("shaka-hidden");
    $(".shaka-forward-button").removeClass("shaka-hidden");
    $(".lockButton").removeClass("shaka-hidden");
    $(".shaka-overflow-button").removeClass("shaka-hidden");
 });



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


console.log(HLSmanifestUri);
  if (typeof isArray.adsEnable !== 'undefined' && isArray.adsEnable) {
    var mediaTailorAdsParams = {};
       if(Object.keys(isArray.adsParam).length > 0){
          mediaTailorAdsParams = {
          "adsParams": isArray.adsParam
          };
       }else{
          mediaTailorAdsParams = {
          "adsParams": {}
          };
       }




// const mediaTailorAdsParams = {
//   "adsParams": {
//     "pod_max_duration": "30",
//     "hobby": "none"
//   }
// };


    var uri = await adManager.requestMediaTailorStream(HLSmanifestUri, mediaTailorAdsParams);

  } else {
    var uri = HLSmanifestUri;

  }


console.log(uri);
  await playerShaka.load(uri);



  ///===============================================================================================
} catch (error) {
  console.error("Error loading manifest:", error);
}


}// player function close

