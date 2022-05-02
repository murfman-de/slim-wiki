<?php

$mode = $data['mode'];
$themeDir = __DIR__ . '/../theme/' . $data['theme'];
$themePath = 'server/theme/' . $data['theme'];

include($themeDir . '/init-theme.php');

?><!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=0">

  <title><?php
    if (count($data['breadcrumbs']) > 1) {
      // Show page name in title
      echo end($data['breadcrumbs'])['name'] . ' &middot; ';
    }
    echo $data['wikiName'];
  ?></title>

  <base href="<?php echo $data['baseUrl']; ?>">

  <?php if ($mode == 'edit') { ?>
  <!-- build:css client/edit.css -->
  <link href="client/libs/CodeMirror/lib/codemirror.css" rel="stylesheet">
  <link href="client/libs/CodeMirror/theme/railscasts.css" rel="stylesheet">
  <!-- endbuild -->
  <?php } // if ($mode == 'edit') ?>

  <!-- build:css client/view.css -->
  <link href="client/libs/prism/themes/prism-default-patched.css" rel="stylesheet" />
  <link href="client/libs/tocbot/tocbot.css" rel="stylesheet" />

  <link href=".tmp/app-view.css" rel="stylesheet" />
  <!-- endbuild -->

  <?php include($themeDir . '/head.php'); ?>

  <script type="text/javascript">
    window.slimwiki = {
      <?php // We support IE 9+. We need addEventListener, querySelectorAll and JSON.parse  ?>
      supportedBrowser: !! document.addEventListener,
      settings: <?php
        $settings = array(
          'mode' => $mode,
          'showToc' => $data['showToc']
        );
        if ($mode == 'edit' || $mode == 'createArticle') {
          $settings['demoMode'] = $data['demoMode'];
          $settings['pageTitle'] = end($data['breadcrumbs'])['name'];
          $settings['requestPath'] = $data['requestPath'];
          $settings['articleFilename'] = $data['articleFilename'];
        }
        echo json_encode($settings);
      ?>
    };
  </script>

</head>
<body class="mode-<?php echo $mode; ?> is-toc-<?php echo $data['showToc'] ? 'enabled' : 'disabled'; ?>">

<?php

if ($mode != 'view') {
  // Show an error message if JavaScript is off or if the browser is not supported.
  // NOTE: In view mode we don't show an error. Instead, syntax highlighting will be off for unsupported browsers.
  ?><div id="jumbo-message"><div><?php echo ($mode == 'error') ? $data['fatalErrorMessage'] : $i18n['error.noJavaScript']; ?></div>
    <a class="btn btn-default" href="<?php echo $data['requestPath']; ?>"><?php echo $i18n['button.back']; ?></a>
  </div><?php

  if ($mode != 'error') {
    ?><script type="text/javascript">
      (function() {
        var errElem = document.getElementById('jumbo-message');
        if (slimwiki.supportedBrowser) {
          errElem.parentNode.removeChild(errElem);
        } else {
          errElem.firstChild.innerHTML = <?php echo json_encode($i18n['error.browserNotSupported']); ?>;
        }
      })();
    </script>
    <div id="error-alert"><div class="alert alert-warning"><?php echo $i18n['error.errorLogged']; ?></div></div><?php
  }
}

if ($mode == 'edit') {
  ?><div id="editor-wrapper">
    <textarea id="editor"><?php echo str_replace('<', '&lt;', str_replace('&', '&amp;', $data['articleMarkdown'])); ?></textarea>
  </div>
  <script type="text/javascript">
    if (slimwiki.supportedBrowser) {
      document.getElementById('editor-wrapper').style.display = 'block';
    }
  </script>
  <div id="close-edit-mode"><a class="btn btn-default" href="<?php echo $data['requestPath']; ?>">X</a></div><?php
} // if ($mode == 'edit')

?>
<div id="main-wrapper"><?php

  if ($mode == 'edit') {
    ?><script type="text/javascript">
      if (slimwiki.supportedBrowser) {
        document.getElementById('main-wrapper').style.display = 'block';
      }
    </script>
    <?php
  }

  if ($mode == 'view' || $mode == 'edit' || $mode == 'noSuchArticle' || $mode == 'private' || $mode == 'createArticle') {
    include($themeDir . '/page-header.php');
  }

  if ($mode == 'edit' && $data['demoMode']) {
      ?><div id="demo-alert"><div class="alert alert-info clearfix">
        <div><strong><?php echo $i18n['demoAlert.title']; ?></strong> <?php echo $i18n['demoAlert.message']; ?></div>
        <button id="demoAlertOkBtn" class="btn btn-default pull-right"><?php echo $i18n['button.ok']; ?></button>
      </div></div><?php
  }

  if ($mode == 'view' || $mode == 'edit' || $mode == 'private') {
    include($themeDir . '/content.php');
  }

  if ($mode == 'noSuchArticle' || $mode == 'createArticle') {
    ?><div id="jumbo-message"><div><?php echo $i18n['createArticle.text']; ?></div><?php
      if ($mode == 'createArticle') {
        ?><button id="createArticleBtn" class="btn btn-default""><?php echo $i18n['button.createArticle']; ?></button><?php
      }
    ?></div><?php
  }

  if ($mode == 'createUser') {
    ?><form id="create-user-box" onsubmit="return false">
      <div class="form-group">
        <label for="user"><?php echo $i18n['createUser.userName']; ?></label>
        <input type="text" class="form-control" id="user" placeholder="<?php echo $i18n['createUser.userName']; ?>">
      </div>
      <div class="form-group">
        <label for="password"><?php echo $i18n['createUser.password']; ?></label>
        <input type="password" class="form-control" id="password" placeholder="<?php echo $i18n['createUser.password']; ?>">
      </div>
      <button id="showConfigBtn" class="btn btn-primary"><?php echo $i18n['createUser.showConfig']; ?></button>
      <a class="btn btn-default pull-right" href="<?php echo $data['requestPath']; ?>"><?php echo $i18n['button.cancel']; ?></a>
      <div id="result-box" class="markdown">
        <?php echo $i18n['createUser.addToConfig']; ?>
        <pre><code id="result"></code></pre>
      </div>
    </form><?php
  } // if ($mode == 'createUser')

  include($themeDir . '/page-footer.php');
  ?>

</div><?php // id="main-wrapper" ?>

<?php if ($mode == 'edit' || $mode == 'createArticle' || $mode == 'createUser') { ?>
<!-- build:js client/edit.js -->
  <script src="client/libs/CodeMirror/lib/codemirror.js"></script>
  <script src="client/libs/CodeMirror/addon/mode/overlay.js"></script> <!-- Allow language-in-language -->
  <script src="client/libs/CodeMirror/mode/markdown/markdown.js"></script>
  <script src="client/libs/CodeMirror/mode/gfm/gfm-patched.js"></script>

  <!-- Nested languages -->
  <script src="client/libs/CodeMirror/mode/clike/clike.js"></script>
  <script src="client/libs/CodeMirror/mode/css/css.js"></script>
  <script src="client/libs/CodeMirror/mode/htmlmixed/htmlmixed.js"></script>
  <script src="client/libs/CodeMirror/mode/javascript/javascript.js"></script>
  <script src="client/libs/CodeMirror/mode/xml/xml.js"></script>

  <script src="client/libs/CodeMirror/mode/meta.js"></script>

  <script src="client/js/app-edit.js"></script>
<!-- endbuild -->
<?php } // if ($mode == 'edit' || $mode == 'createUser') ?>

<?php if ($mode != 'error') { ?>
<!-- build:js client/view.js -->
  <script src="client/libs/prism/prism-patched.js"></script>
  <script src="client/libs/prism/plugins/autoloader/prism-autoloader.js"></script>
  <script src="client/libs/tocbot/tocbot.js"></script>

  <script src="client/js/app-view.js"></script>
<!-- endbuild -->
<?php } // if ($mode != 'error') ?>

</body>
</html>
