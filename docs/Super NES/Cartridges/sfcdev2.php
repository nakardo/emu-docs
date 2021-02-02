<html>

<head>
<title>Super Nintendo 1024K HiROM Devcart</title>
</head>

<body bgcolor="#222266" text="#FFFFFF" link="#FFFFFF" vlink="#FFFFFF">

<center><h2>Super Nintendo 1024K HiROM Devcart</h2>By Charles MacDonald</center>
<hr>

<p>
<b>Disclaimer</b>
<ul>
    <li> Perform this modification at your own risk.
    <li> I will not make or sell any SNES devcarts.
</ul>
</p>

<p>
    And the usual "I wrote this, so don't copy" applies.<br>
    Don't link to this page or host it at your website - please link to mine using this URL: <a href="http://cgfm2.emuviews.com"><pre>http://cgfm2.emuviews.com</pre></a>
    This way everybody can get the most up-to-date version and I won't have to answer questions about older revisions!
</p>


<b>Introduction</b>
<p>
    This document will describe how to modify a "Street Fighter II Turbo" cartridge to support two Am29F040 flash memory chips.
    I'll try to give enough background information so the same ideas can be applied to other cartridges as well.
</p>

<b>Overview</b>

<p>
    All licensed SNES games 8-bit mask ROMs, that usually have a non-standard pinout compared to similar EPROM parts.
    Nintendo did the same thing with the NES, presumably to optimize the PCB layout of cartridges, or perhaps just to be difficult.
    Typical mask ROM manufacturers for Nintendo are Sharp, Fujitsu, Sony, Toshiba, etc.
</p>

<p>
    SNES games tend to come in several varieties:

    <ul>
    <li> Has one small capacity ROM. (Many early and other small games)
    <li> Has several small capacity ROMs. (Ogre Battle, Metroid 3)
    <li> Has one large capacity ROM. (Street Fighter II, Killer Instinct)
    <li> Has several large capacity ROMs. (Street Fighter II Turbo)
    </ul>
</p>

<p>
    Games with more than one ROM need additional logic for the address decoding.
    Address decoding is the process of mapping the ROMs to memory, where only one of several is enabled at any given time.
    If you are replacing ROMs with memory of a different size, the address decoding logic needs to be altered to accomodate this,
    otherwise the ROMs will be partially accessible (replacing with larger memory) or have 'gaps' between them of mirrored data (replacing with smaller memory).
</p>
<p>
    Address decoding is typically handled in TTL logic, though some newer games use the Nintendo MAD-1 chip.
    I'll only cover what to do for TTL logic, I haven't worked with any cartridges utilizing the MAD-1 yet.
</p>

<p>
    There are two ways the ROM(s) in a cartridge are mapped to memory, called LoROM and HiROM.

    The main differences are:
    <ul>
    <li> <b>LoROM</b>: ROM data mapped to upper 32K of each 64K bank. Occupies banks $00-$3F and $80-$BD.
    <li> <b>HiROM</b>: ROM data mapped to all 64K of each 64K bank. Occupies banks $40-$7D and $C0-$FF.
    </ul>

    Typically most small to medium sized games are in LoROM format, and the larger ones are HiROM.
    There are many exceptions, of course.
</p>

<b>Cartridge Details</b>
<p>
    For this project I used Capcom's "Street Fighter II Turbo" cartridge, which uses the HiROM configuration and should be easy to find due to it's popularity.
    It's PCB ID string is "SHVC-BJON-01", other variations of the PCB may not work with this modification.
    It uses the following components:

<ul>
    <li> Sharp LH537T08 2048Kx8 mask ROM
    <li> CAT534010A 1024x8 mask ROM
    <li> Nintendo CIC chip (p/n D411A)
    <li> Hitachi HD74LS00P (Quad NAND gate)
</ul>

    The CIC chip is used to enforce regional lockout between PAL (Europe)
    and NTSC (USA, Japan) consoles. D411A is the USA NTSC variant.
    The 74LS00 is used for address decoding.
    It's connection in the PCB is as follows:

<pre>
                     +--\/--+
         (+5V)   1A -|01  14|- Vcc  (+5V)
(SNES ROM /OE)   1B -|02  13|- 4B   (SNES A21)
  (to 4A & 3B)   1Y -|03  12|- 4A   (from 1Y)
         (+5V)   2A -|04  11|- 4Y   (out to ROM #2 /OE)
    (SNES A21)   2B -|05  10|- 3B   (from 1Y)
       (to 3A)   2Y -|06  09|- 3A   (from 2Y)
         (GND)  GND -|07  08|- 3Y   (out to ROM #1 /OE)
                     +------+

 ROM #1 /OE is enabled when SNES A21 is low and SNES ROM /OE is low.
 ROM #2 /OE is enabled when SNES A21 is high and SNES ROM /OE is low.
</pre>

 This arrangement gives the following memory map:

<pre>
    $C00000-$DFFFFF : ROM #1
    $E00000-$EFFFFF : ROM #2
    $F00000-$FFFFFF : ROM #2 (mirror)
</pre>

 HiROM games commonly access the ROM at banks $C0-$FF exclusively,
 so I'm showing only the relevant portion of the memory map.
</p>

<p>
 The two mask ROMs have the following (non standard) pinout:

<pre>
 Sharp LH537T08 (36-pin DIP)
 Corresponding Am29F040 pin names are in brackets.

                     +--\/--+
                A20 -|01  36|- +5V
                +5V -|02  35|- +5V
          [A18] A17 -|03  34|- +5V
          [A16] A18 -|04  33|- /OE [WE#]
                A15 -|05  32|- A19 [A17]
                A12 -|06  31|- A14
                 A7 -|07  30|- A13
                 A6 -|08  29|- A8
                 A5 -|09  28|- A9
                 A4 -|10  27|- A11
                 A3 -|11  26|- A16 [OE#]
                 A2 -|12  25|- A10
                 A1 -|13  24|- /CE
                 A0 -|14  23|- D7
                 D0 -|15  22|- D6
                 D1 -|16  21|- D5
                 D2 -|17  20|- D4
                GND -|18  19|- D3
                     +------+

 CAT534010A (32-pin DIP)
 Corresponding Am29F040 pin names are in brackets.
 I've included descriptions the top 4 unused pins of the 36-pin socket,
 the ROM is mounted in the lower 32 holes.

                     +--\/--+
                A20 -|xx  xx|- +5V
                +5V -|xx  xx|- +5V
                     +--\/--+
          [A18] A17 -|01  32|- +5V
          [A16] A18 -|02  31|- /OE [WE#]
                A15 -|03  30|- A19 [A17]
                A12 -|04  29|- A14
                 A7 -|05  28|- A13
                 A6 -|06  27|- A8
                 A5 -|07  26|- A9
                 A4 -|08  25|- A11
                 A3 -|09  24|- A16 [OE#]
                 A2 -|10  23|- A10
                 A1 -|11  22|- /CE
                 A0 -|12  21|- D7 
                 D0 -|13  20|- D6 
                 D1 -|14  19|- D5
                 D2 -|15  18|- D4
                GND -|16  17|- D3
                     +------+
</pre>
</p>

<b>Modification Details</b>

<p>
    The goal is to rewire part of the board so the ROM sockets are compatible with Am29F040 flash memory, and to modify the address decoding logic so it will support the two smaller memory chips instead of a 2048K and 1024K one.
</p>

<b> Parts list </b>
<p>
<ul>
    <li> Two Am29F040B-PC90 flash memory ICs (DIP package). [<a href="http://www.amd.com">AMD</a>]
    <li> Two 32-pin machine tooled DIP sockets. [Pan Pacific]
    <li> Two 32-pin solder tail DIP sockets. [Pan Pacific]
    <li> Two 32-pin ZIF-type DIP sockets. [<a href="http://www.arieselec.com">Aries Electronics</a>]
    <li> Shielded wire. Any type of ribbon cable is cheap and ideal for this. You'll need five pieces, only a few inches in length.
    <li> Electrical tape, soldering iron, desoldering tools. (I used a solder "sucker" and desoldering braid)
</ul>

These items can be purchased from companies like <a href="http://www.jameco.com">Jameco</a>, <a href="http://www.digikey.com">Digikey</a>, <a href="http://www.mouser.com">Mouser</a>, etc.
</p>

<p>
The modifications needed are as follows:

<pre>
   Desolder and remove both mask ROMs. Insert short wires in the holes for
   pins A17, A18, /OE, and A16 for both ROMs. Now insert and solder
   in the machine tooled sockets.

   For the solder tail sockets, bend out pins 1,2,24,30 and remove
   the entire contact assembly for pin 31. (this is so WE# will remain
   unconnected) Place electrical tape over the first sockets in
   the corresponding positions for these pins so they won't make contact
   with the pin holes even in their bent out position.

   Insert the second set of sockets, and connect the wire links as follows:
   Wire from A17 goes to bent out pin 30 (A17) on the second socket.
   Wire from A18 goes to bent out pin 1 (A18) on the second socket.
   Wire from /OE goes to bent out pin 24 (/OE) on the second socket.
   Wire from A16 goes to bent out pin 2 (A16) on the second socket.

   Now we will replace the SNES A21 input to the 74LS00 with SNES A19. This
   only has to be done for 74LS00 pin 13 since pin 5 is connected to pin 13.
   Cut the trace leaving pin 13 towards the edge connector, then tap SNES A19
   elsewhere (e.g. A19 from either mask ROM) and link the wire to pin 13.

   I'd advise double-checking that the trace is completely cut with a
   multimeter, checking pin 13 of the 74LS00 and SNES A21 which is edge
   connector pin 46.
</pre>
</p>

<p>
 The cartridge is complete. There's just enough room to add two 32-pin ZIF
 sockets side by side to make swapping flash memory easier. The bottom
 socket close to the edge connector is for the first 512K of program code,
 and the top socket is for the last 512K. The new memory map is:
<pre>
    $C00000-$C7FFFF : Am29F040 #1 (bottom socket)
    $C80000-$CFFFFF : Am29F040 #2 (top socket)
</pre>
</p>
<p>
 Remember that the flash memory chips will face out backwards from the cartridge,
 which will no longer fit in a SNES unless you use something like a Super
 Famicom converter, Pro Action Replay, or remove the top part of the SNES case
 to give more room for the cartridge. A Game Genie's plastic case doesn't
 give enough clearance to work.
</p>
<p>
 In my experience the WE# pin of the Am29F040 seems to have a internal pull-up resistor so it can be left unconnected without causing spurious writes.
 This may not be true for other types of flash memory, if you use something else tie WE# to +5V.
 Or you could make the flash writable by connecting WE# to SNES /WR, though I haven't tried this myself.
</p>

<p>
    There aren't many small HiROM games available, here are the ones I've tried for testing the devcart:
    <ul>
        <li> Psycho Dream
        <li> The Shinri Game 3
        <li> Same Same Game
    </ul>
</p>

<b>Credits and Acknowledgements</b>

<ul>
    <li> Siudym for documenting the mask ROMs, 74LS00, and SNES cartridge connector pin assignments.
    <li> Jeff Frohwein for the SNES cartridge connector pinout.
</ul>

<hr>

</body>
</html>

