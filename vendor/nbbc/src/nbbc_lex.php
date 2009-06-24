<?php

	//-----------------------------------------------------------------------------
	//
	//  nbbc_lex.php
	//
	//  This file is part of NBBC, the New BBCode Parser.
	//
	//  NBBC implements a fully-validating, high-speed, extensible parser for the
	//  BBCode document language.  Its output is XHTML 1.0 Strict conformant no
	//  matter what its input is.  NBBC supports the full standard BBCode language,
	//  as well as comments, columns, enhanced quotes, spoilers, acronyms, wiki
	//  links, several list styles, justification, indentation, and smileys, among
	//  other advanced features.
	//
	//-----------------------------------------------------------------------------
	//
	//  Copyright (c) 2008, the Phantom Inker.  All rights reserved.
	//
	//  Redistribution and use in source and binary forms, with or without
	//  modification, are permitted provided that the following conditions
	//  are met:
	//
	//    * Redistributions of source code must retain the above copyright
	//       notice, this list of conditions and the following disclaimer.
	//
	//    * Redistributions in binary form must reproduce the above copyright
	//       notice, this list of conditions and the following disclaimer in
	//       the documentation and/or other materials provided with the
	//       distribution.
	//
	//  THIS SOFTWARE IS PROVIDED BY THE PHANTOM INKER "AS IS" AND ANY EXPRESS
	//  OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
	//  WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
	//  DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
	//  LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
	//  CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
	//  SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR
	//  BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,
	//  WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE
	//  OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN
	//  IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
	//
	//-----------------------------------------------------------------------------
	//
	//  This file implements the NBBC lexical analyzer, which breaks down the
	//  input text from characters into tokens.  This uses an event-based
	//  interface, somewhat like lex or flex uses, wherein each time
	//  $this->NextToken is called, the next token is returned until it returns
	//  BBCODE_EOI at the end of the input.
	//
	//-----------------------------------------------------------------------------

	class BBCodeLexer {
		var $token;			// Return token type:  One of the BBCODE_* constants.
		var $text;			// Actual exact, original text of token.
		var $tag;			// If token is a tag, this is the decoded array version.
		
		var $state;			// Next state of the lexer's state machine: text, or tag/ws/nl
		var $input;			// The input string, split into an array of tokens.
		var $ptr;			// Read pointer into the input array.
		var $unget;			// Whether to "unget" the last token.
		
		var $verbatim;		// In verbatim mode, we return all input, unparsed, including comments.

		var $tagmarker;		// Which kind of tag marker we're using:  "[", "<", "(", or "{"
		var $pat_main;		// Main tag-matching pattern.
		var $pat_comment;	// Pattern for matching comments.
		var $pat_comment2;	// Pattern for matching comments.
		var $pat_wiki;		// Pattern for matching wiki-links.

		function BBCodeLexer($string, $tagmarker = '[') {
			// First thing we do is to split the input string into tuples of
			// text and tags.  This will make it easy to tokenize.  We define a tag as
			// anything starting with a [, ending with a ], and containing no [ or ] in
			// between unless surrounded by "" or '', and containing no newlines.
			// We also separate out whitespace and newlines.
			
			// Choose a tag marker based on the possible tag markers.
			$beginmarkers = Array( '[' => '\[', '<' => '<', '{' => '\{', '(' => '\(' );
			$endmarkers   = Array( '[' => '\]', '<' => '>', '{' => '\}', '(' => '\)' );
			if (!isset($endmarkers[$tagmarker])) $tagmarker = '[';
			$e = $endmarkers[$tagmarker];
			$b = $beginmarkers[$tagmarker];
			$this->tagmarker = $tagmarker;
			
			// $this->input will be an array of tokens, with the special property that
			// the elements strictly alternate between plain text and tags/whitespace/newlines,
			// and that tags always have *two* entries per tag.  The first element will
			// always be plain text.  Note that the regexes below make VERY heavy use of
			// PCRE regex-syntax extensions, so don't even try to modify them unless you
			// know how things like (?!) and (?:) and (?=) work.  We use the /x modifier
			// here to make this a *lot* more legible and debuggable.
			$this->pat_main = "/( "
				// Match tags, as long as they do not start with [-- or [' or [!-- or [rem or [[.
				// Tags may contain "quoted" or 'quoted' sections that may contain [ or ] characters.
				// Tags may not contain newlines.
				. "{$b}"
				. "(?! -- | ' | !-- | {$b}{$b} )"
				. "(?: [^\\n\\r{$b}{$e}] | \\\" [^\\\"\\n\\r]* \\\" | \\' [^\\'\\n\\r]* \\' )*"
				. "{$e}"

				// Match wiki-links, which are of the form [[...]] or [[...|...]].  Unlike
				// tags, wiki-links treat " and ' marks as normal input characters; but they
				// still may not contain newlines.
				. "| {$b}{$b} (?: [^{$e}\\r\\n] | {$e}[^{$e}\\r\\n] )* {$e}{$e}"

				// Match single-line comments, which start with [-- or [' or [rem .
				. "| {$b} (?: -- | ' ) (?: [^{$e}\\n\\r]* ) {$e}"

				// Match multi-line comments, which start with [!-- and end with --] and contain
				// no --] in between.
				. "| {$b}!-- (?: [^-] | -[^-] | --[^{$e}] )* --{$e}"

				// Match five or more hyphens as a special token, which gets returned as a [rule] tag.
				. "| -----+"

				// Match newlines, in all four possible forms.
				. "| \\x0D\\x0A | \\x0A\\x0D | \\x0D | \\x0A"

				// Match whitespace, but only if it butts up against a newline, rule, or
				// bracket on at least one end.
				. "| [\\x00-\\x09\\x0B-\\x0C\\x0E-\\x20]+(?=[\\x0D\\x0A{$b}]|-----|$)"
				. "| (?<=[\\x0D\\x0A{$e}]|-----|^)[\\x00-\\x09\\x0B-\\x0C\\x0E-\\x20]+"

				. " )/Dx";

			$this->input = preg_split($this->pat_main, $string, -1, PREG_SPLIT_DELIM_CAPTURE);

			// Patterns for matching specific types of tokens during lexing.
			$this->pat_comment = "/^ {$b} (?: -- | ' ) /Dx";
			$this->pat_comment2 = "/^ {$b}!-- (?: [^-] | -[^-] | --[^{$e}] )* --{$e} $/Dx";
			$this->pat_wiki = "/^ {$b}{$b} ([^\\|]*) (?:\\|(.*))? {$e}{$e} $/Dx";
			
			// Current lexing state.
			$this->ptr = 0;
			$this->unget = false;
			$this->state = BBCODE_LEXSTATE_TEXT;
			$this->verbatim = false;
			
			// Return values.
			$this->token = BBCODE_EOI;
			$this->tag = false;
			$this->text = "";
		}

		// Compute how many non-tag characters there are in the input, give or take a few.
		// This is optimized for speed, not accuracy, so it'll get some stuff like
		// horizontal rules and weird whitespace characters wrong, but it's only supposed
		// to provide a rough quick guess, not a hard fact.
		function GuessTextLength() {
			$length = 0;
			$ptr = 0;
			$state = BBCODE_LEXSTATE_TEXT;
			
			// Loop until we find a valid (nonempty) token.
			while ($ptr < count($this->input)) {
				$text = $this->input[$ptr++];
				
				if ($state == BBCODE_LEXSTATE_TEXT) {
					$state = BBCODE_LEXSTATE_TAG;
					$length += strlen($text);
				}
				else {
					switch (ord(substr($this->text, 0, 1))) {
					case 10:
					case 13:
						$state = BBCODE_LEXSTATE_TEXT;
						$length++;
						break;
					default:
						$state = BBCODE_LEXSTATE_TEXT;
						$length += strlen($text);
						break;
					case 40:
					case 60:
					case 91:
					case 123:
						$state = BBCODE_LEXSTATE_TEXT;
						break;
					}
				}
			}

			return $length;
		}

		// Return the type of the next token, either BBCODE_TAG or BBCODE_TEXT or
		// BBCODE_EOI.  This stores the content of this token into $this->text, the
		// type of this token in $this->token, and possibly an array into $this->tag.
		//
		// If this is a BBCODE_TAG token, $this->tag will be an array computed from
		// the tag's contents, like this:
		//    Array(
		//       '_name' => tag_name,
		//       '_end' => true if this is an end tag (i.e., the name starts with a /)
		//       '_default' => default value (for example, in [url=foo], this is "foo").
		//       ...
		//       ...all other key => value parameters given in the tag...
		//       ...
		//    )
		function NextToken() {
		
			// Handle ungets; if the last token has been "ungotten", just return it again.
			if ($this->unget) {
				$this->unget = false;
				return $this->token;
			}
			
			// Loop until we find a valid (nonempty) token.
			while (true) {

				// Did we run out of tokens in the input?
				if ($this->ptr >= count($this->input)) {
					$this->text = "";
					$this->tag = false;
					return $this->token = BBCODE_EOI;
				}
				
				// Inhale one token, sanitizing away any weird control characters.  We
				// allow \t, \r, and \n to pass through, but that's it.
				$this->text = preg_replace("/[\\x00-\\x08\\x0B-\\x0C\\x0E-\\x1F]/", "",
					$this->input[$this->ptr++]);
				
				if ($this->verbatim) {
					// In verbatim mode, we return *everything* as plain text.
					$this->tag = false;
					if ($this->state == BBCODE_LEXSTATE_TEXT)
						$this->state = BBCODE_LEXSTATE_TAG;
					else $this->state = BBCODE_LEXSTATE_TEXT;
					if (strlen($this->text) > 0)
						return $this->token = BBCODE_TEXT;
				}
				else if ($this->state == BBCODE_LEXSTATE_TEXT) {
					// Next up is plain text, but only return it if it's nonempty.
					$this->state = BBCODE_LEXSTATE_TAG;
					$this->tag = false;
					if (strlen($this->text) > 0)
						return $this->token = BBCODE_TEXT;
				}
				else {
					// This must be either whitespace, a newline, or a tag.
					switch (ord(substr($this->text, 0, 1))) {
					case 10:
					case 13:
						// Newline.
						$this->tag = false;
						$this->state = BBCODE_LEXSTATE_TEXT;
						return $this->token = BBCODE_NL;
					case 45:
						// A rule made of hyphens; return it as a [rule] tag.
						if (preg_match("/^-----/", $this->text)) {
							$this->tag = Array('_name' => 'rule', '_endtag' => false, '_default' => '');
							$this->state = BBCODE_LEXSTATE_TEXT;
							return $this->token = BBCODE_TAG;
						}
						else {
							$this->tag = false;
							$this->state = BBCODE_LEXSTATE_TEXT;
							if (strlen($this->text) > 0)
								return $this->token = BBCODE_TEXT;
							continue;
						}
					default:
						// Whitespace.
						$this->tag = false;
						$this->state = BBCODE_LEXSTATE_TEXT;
						return $this->token = BBCODE_WS;
					case 40:
					case 60:
					case 91:
					case 123:
						// Tag or comment.  This is the most complicated one, because it
						// needs to be parsed into its component pieces.
						
						// See if this is a comment; if so, skip it.
						if (preg_match($this->pat_comment, $this->text)) {
							// This is a comment, not a tag, so treat it like it doesn't exist.
							continue;
						}
						if (preg_match($this->pat_comment2, $this->text)) {
							// This is a comment, not a tag, so treat it like it doesn't exist.
							continue;
						}
						
						// See if this is a [[wiki link]]; if so, convert it into a [wiki="" title=""] tag.
						if (preg_match($this->pat_wiki, $this->text, $matches)) {
							$this->tag = Array('_name' => 'wiki', '_endtag' => false,
								'_default' => @$matches[1], 'title' => @$matches[2]);
							$this->state = BBCODE_LEXSTATE_TEXT;
							return $this->token = BBCODE_TAG;
						}
						
						// Not a comment, so parse it like a tag.
						$this->tag = $this->Internal_DecodeTag($this->text);
						$this->state = BBCODE_LEXSTATE_TEXT;
						return $this->token = ($this->tag['_end'] ? BBCODE_ENDTAG : BBCODE_TAG);
					}
				}
			}
		}
		
		// Ungets the last token read so that a subsequent call to NextToken() will
		// return it.  Note that UngetToken() does not switch states when you switch
		// between verbatim mode and standard mode:  For example, if you read a tag,
		// unget the tag, switch to verbatim mode, and then get the next token, you'll
		// get back a BBCODE_TAG --- exactly what you ungot, not a BBCODE_TEXT token.
		function UngetToken() {
			if ($this->token !== BBCODE_EOI)
				$this->unget = true;
		}

		// Peek at the next token, but don't remove it.
		function PeekToken() {
			$result = $this->NextToken();
			if ($this->token !== BBCODE_EOI)
				$this->unget = true;
			return $result;
		}

		// Save the state of this lexer so it can be restored later.  The return
		// value from this should be considered opaque.  Because PHP uses copy-on-write
		// references, the total cost of the returned state is relatively small, and
		// the running time of this function (and RestoreState) is very fast.
		function SaveState() {
			return Array(
				'token' => $this->token,
				'text' => $this->text,
				'tag' => $this->tag,
				'state' => $this->state,
				'input' => $this->input,
				'ptr' => $this->ptr,
				'unget' => $this->unget,
				'verbatim' => $this->verbatim
			);
		}
		
		// Restore the state of this lexer from a saved previous state.
		function RestoreState($state) {
			if (!is_array($state)) return;
			$this->token = @$state['token'];
			$this->text = @$state['text'];
			$this->tag = @$state['tag'];
			$this->state = @$state['state'];
			$this->input = @$state['input'];
			$this->ptr = @$state['ptr'];
			$this->unget = @$state['unget'];
			$this->verbatim = @$state['verbatim'];
		}

		// Given a string, if it's surrounded by "quotes" or 'quotes', remove them.
		function Internal_StripQuotes($string) {
			if (preg_match("/^\\\"(.*)\\\"$/", $string, $matches))
				return $matches[1];
			else if (preg_match("/^\\'(.*)\\'$/", $string, $matches))
				return $matches[1];
			else return $string;
		}

		// Given a string containing a complete [tag] (including its brackets), break
		// it down into its components and return them as an array.
		function Internal_DecodeTag($tag) {
			// Strip off the [brackets] around the tag, leaving just its content.
			$tag = substr($tag, 1, strlen($tag)-2);
			
			// The starting bracket *must* be followed by a non-whitespace character.
			$ch = ord(substr($tag, 0, 1));
			if ($ch >= 0 && $ch <= 32)
				return Array('_name' => '', '_end' => false, '_default' => false);

			// Break it apart into words, quoted text, whitespace, and equal signs, and leave out the whitespace.
			$pieces = preg_split("/(\\\"[^\\\"]+\\\"|\\'[^\\']+\\'|=)|[\\x00-\\x20]+/",
				$tag, -1, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);
			$ptr = 0;

			// Handle malformed (empty) tags correctly.
			if (count($pieces) < 1)
				return Array('_name' => '', '_end' => false, '_default' => false);

			// The first piece should be the tag name, whatever it is.  If it starts with a /
			// we remove the / and mark it as an end tag.
			$result = Array();
			if (@substr($pieces[$ptr], 0, 1) == '/') {
				$result['_name'] = strtolower(substr($pieces[$ptr++], 1));
				$result['_end'] = true;
			}
			else {
				$result['_name'] = strtolower($pieces[$ptr++]);
				$result['_end'] = false;
			}
			
			// If the second piece is an equal sign, then the third piece is the tag's default value.
			if (@$pieces[$ptr] == '=') {
				$ptr++;
				// See if this first parameter is quoted; if not, we need to collect.
				$ch = @substr($pieces[$ptr], 0, 1);
				if ($ch == "'" || $ch == "\"")
					$value = $this->Internal_StripQuotes(@$pieces[$ptr++]);
				else {
					// Collect pieces going forward until we reach an = sign or the end of the
					// tag; then rewind before whatever comes before the = sign, and everything
					// between here and there becomes the default value.  This allows tags like
					// [font=Times New Roman size=4] to make sense even though the font name is
					// not quoted.
					$start = $ptr;
					while ($ptr < count($pieces)-1 && $pieces[$ptr] != '=')
						$ptr++;
					if (@$pieces[$ptr] == '=') {
						// Rewind before = sign.
						$ptr--;
						// Rewind before any whitespace before = sign.
						while ($ptr > $start && preg_match("/^[\\x00-\\x20]+$/", $pieces[$ptr]))
							$ptr--;
						// Rewind before any non-whitespace before = sign.
						if ($ptr > $start && !preg_match("/^[\\x00-\\x20]+$/", $pieces[$ptr]))
							$ptr--;
					}
					// The default value is everything from $start to $ptr, inclusive.
					$value = "";
					for (; $start <= $ptr; $start++) {
						if (strlen($value) > 0) $value .= " ";
						$value .= $this->Internal_StripQuotes(@$pieces[$start]);
					}
				}
				$result['_default'] = $value;
			}
			else $result['_default'] = false;

			// The rest of the tag is composed of either floating keys or key=value pairs, so walk through
			// the tag and collect them all.
			while ($ptr < count($pieces)) {
				$key = strtolower($pieces[$ptr++]);
				if (@$pieces[$ptr] == '=') {
					$ptr++;
					$value = $this->Internal_StripQuotes(@$pieces[$ptr++]);
				}
				else $value = $this->Internal_StripQuotes($key);
				if ($key != '_end' && $key != '_name' && $key != '_default')
					$result[$key] = $value;
			}
			
			// Save the resulting parameters, and return the whole shebang.
			return $result;
		}
	}

?>