//YouTube parser to strip out the other parts
function youtube_parser(url)
{
    let re = /^(https?:\/\/)?((www\.)?(youtube(-nocookie)?|youtube.googleapis)\.com.*(v\/|v=|vi=|vi\/|e\/|embed\/|user\/.*\/u\/\d+\/)|youtu\.be\/)([_0-9a-z-]+)/i;
    let id = url.match(re)[7];
    return id;
}

//Generate the link
function vidyen_link_generate()
{
  //if (document.getElementById("xmrwallet").value.length == 0)
  if (document.getElementById("xmrwallet").value.length < 90)
  {
    alert("Enter a valid XMR Wallet.");
    return;
  }
  else
  {
    var xrm_wallet = document.getElementById("xmrwallet").value;
  }

  //if (document.getElementById("xmrwallet").value.length == 0)
  if (document.getElementById("yt_url").value < 5)
  {
    alert("Enter a valid YouTube Link");
    return;
  }
  else
  {
    var url = document.getElementById("yt_url").value;
    yt_url = youtube_parser(url);
  }
  var pool = ''; //Blank this out in case next is null
  pool = document.getElementById("poolinput").value;

  var pool_pass = ''; //Blank this out in case next is null
  pool_pass = document.getElementById("pool_pass").value;

  //Fit the pool pass as people might get cute
  //var pool_pass_prespace = removeSpaces(pool_pass);
  var pool_pass = pool_pass.replace("=", "equalsign");
  var pool_pass = pool_pass.replace(":", "colonsymbol");
  var pool_pass = pool_pass.replace("@", "atsymbol");
  var pool_pass = pool_pass.replace(".", "dotsymbol");
  var pool_pass = pool_pass.replace("-", "dashsymbol");

  var pool_pass = pool_pass.replace(/[^a-zA-Z0-9-_]/g, '');

  var pool_checked = pool_check(pool);

  var vh_url_output = "https://vidhash.com/?xmrwallet=" + xrm_wallet + '&youtube=' + yt_url + '&pool=' + pool_checked + '&pool_pass=' + pool_pass;

  document.getElementById("url_output").value = vh_url_output;
}

//Copy link.
function copy_link()
{
  var copyText = document.getElementById("url_output");
  copyText.select();
  document.execCommand("copy");
  alert("Copied the URL: " + copyText.value);
}

function pool_check(pool)
{
  var pool_array =
  [
    'xmrpool.eu',
    'moneropool.com',
    'monero.crypto-pool.fr',
    'monerohash.com',
    'minexmr.com',
    'usxmrpool.com',
    'supportxmr.com',
    'moneroocean.stream:100',
    'moneroocean.stream',
    'poolmining.org',
    'minemonero.pro',
    'xmr.prohash.net',
    'minercircle.com',
    'xmr.nanopool.org',
    'xmrminerpro.com',
    'clawde.xyz',
    'dwarfpool.com',
    'xmrpool.net',
    'monero.hashvault.pro',
    'osiamining.com',
    'killallasics',
    'arhash.xyz',
    'aeon-pool.com',
    'minereasy.com',
    'aeon.sumominer.com',
    'aeon.rupool.tk',
    'aeon.hashvault.pro',
    'aeon.n-engine.com',
    'aeonpool.xyz',
    'aeonpool.dreamitsystems.com',
    'aeonminingpool.com',
    'aeonhash.com',
    'durinsmine.com',
    'aeon.uax.io',
    'aeon-pool.sytes.net',
    'aeonpool.net',
    'supportaeon.com',
    'pooltupi.com',
    'aeon.semipool.com',
    'turtlepool.space',
    'masari.miner.rocks',
    'etn.spacepools.org',
    'etn.nanopool.org',
    'etn.hashvault.pro'
  ];

  if ( pool_array.includes(pool) )
  {
    //
  }
  else
  {
    pool = 'moneroocean.stream'
    //pool = 'notfound.com';
  }

  return pool;
}
