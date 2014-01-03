freepbx-callfilter
==================

FreePBX module for white/blacklisting incoming calls with _NXXXXXX-style pattern matching. Based in part on the Blacklist module.

Whitelisting takes precedence over blacklisting. If an entry is not found in either list, the call is passed through normally.