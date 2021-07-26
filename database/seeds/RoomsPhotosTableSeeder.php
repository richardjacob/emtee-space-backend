<?php

use Illuminate\Database\Seeder;

class RoomsPhotosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('rooms_photos')->delete();
  	
      DB::table('rooms_photos')->insert([
        ["id"=>1,"room_id"=>10001,"name"=>"MakentDefault/ymnc2183ix7piuhh46iv","highlights"=>"","featured"=>"No"],
        ["id"=>2,"room_id"=>10001,"name"=>"MakentDefault/ilddngmn8rpq0rfsqn3r","highlights"=>"","featured"=>"No"],
        ["id"=>3,"room_id"=>10001,"name"=>"MakentDefault/vmapu0wzdplblmutokcv","highlights"=>"","featured"=>"No"],
        ["id"=>4,"room_id"=>10001,"name"=>"MakentDefault/rblfdanguqyjqy1faqz2","highlights"=>"","featured"=>"No"],
        ["id"=>5,"room_id"=>10001,"name"=>"MakentDefault/ma11pfudrtevfyfrs3pw","highlights"=>"","featured"=>"No"],
        ["id"=>6,"room_id"=>10001,"name"=>"MakentDefault/c6ck2jhwl6uerlxfvuxt","highlights"=>"","featured"=>"Yes"],
        ["id"=>7,"room_id"=>10010,"name"=>"MakentDefault/cfzufegurucp9g5vdedn","highlights"=>"","featured"=>"No"],
        ["id"=>8,"room_id"=>10010,"name"=>"MakentDefault/hyan1a5fz57st1dl3hst","highlights"=>"","featured"=>"No"],
        ["id"=>9,"room_id"=>10010,"name"=>"MakentDefault/enxhp0sxoiz697mh9gti","highlights"=>"","featured"=>"No"],
        ["id"=>10,"room_id"=>10010,"name"=>"MakentDefault/aiuf4zzqhif453nchiof","highlights"=>"","featured"=>"No"],
        ["id"=>11,"room_id"=>10010,"name"=>"MakentDefault/twguhljpyobjapbhirco","highlights"=>"","featured"=>"No"],
        ["id"=>12,"room_id"=>10010,"name"=>"MakentDefault/iiz2ihfc7b4gmrenqbv3","highlights"=>"","featured"=>"Yes"],
        ["id"=>13,"room_id"=>10010,"name"=>"MakentDefault/cqpi5fxju4zprunusa3l","highlights"=>"","featured"=>"No"],
        ["id"=>20,"room_id"=>10012,"name"=>"MakentDefault/ejnqioo8upewgl2x8qvr","highlights"=>"","featured"=>"No"],
        ["id"=>21,"room_id"=>10012,"name"=>"MakentDefault/gzo65topezno0tgx9rmr","highlights"=>"","featured"=>"No"],
        ["id"=>22,"room_id"=>10012,"name"=>"MakentDefault/icf2sxtjxelvajag1bam","highlights"=>"","featured"=>"Yes"],
        ["id"=>23,"room_id"=>10012,"name"=>"MakentDefault/rji0pzr2xjopb5rsvbtn","highlights"=>"","featured"=>"No"],
        ["id"=>24,"room_id"=>10012,"name"=>"MakentDefault/zsfxin9jdfz4mrywq38h","highlights"=>"","featured"=>"No"],
        ["id"=>25,"room_id"=>10006,"name"=>"MakentDefault/kvw7zpyig92q3esddoxs","highlights"=>"","featured"=>"No"],
        ["id"=>26,"room_id"=>10006,"name"=>"MakentDefault/zdowbhu3yt1kcpwetefv","highlights"=>"","featured"=>"No"],
        ["id"=>27,"room_id"=>10006,"name"=>"MakentDefault/h7u79w3u75oeml6byrcq","highlights"=>"","featured"=>"No"],
        ["id"=>28,"room_id"=>10006,"name"=>"MakentDefault/stj2fr1r9myigkxpz91g","highlights"=>"","featured"=>"No"],
        ["id"=>29,"room_id"=>10006,"name"=>"MakentDefault/w5v5m3qcma4nuaysavhw","highlights"=>"","featured"=>"No"],
        ["id"=>30,"room_id"=>10006,"name"=>"MakentDefault/wcicnwaeodrd2ne0m2kx","highlights"=>"","featured"=>"Yes"],
        ["id"=>31,"room_id"=>10007,"name"=>"MakentDefault/mrxthyohv8xnqszzpv0z","highlights"=>"","featured"=>"No"],
        ["id"=>32,"room_id"=>10007,"name"=>"MakentDefault/ppar3wtzr783y2fe9yfk","highlights"=>"","featured"=>"No"],
        ["id"=>33,"room_id"=>10007,"name"=>"MakentDefault/vn2zhrl7o50iov2iuv05","highlights"=>"","featured"=>"No"],
        ["id"=>34,"room_id"=>10007,"name"=>"MakentDefault/gqs214n6ffv5p1emwv1i","highlights"=>"","featured"=>"No"],
        ["id"=>35,"room_id"=>10007,"name"=>"MakentDefault/b3mj1jz9nhqdxswy4dh1","highlights"=>"","featured"=>"No"],
        ["id"=>36,"room_id"=>10007,"name"=>"MakentDefault/ztqshzb0axxcrnlberax","highlights"=>"","featured"=>"Yes"],
        ["id"=>37,"room_id"=>10008,"name"=>"MakentDefault/clfi14uyhrcrjoztfn4z","highlights"=>"","featured"=>"No"],
        ["id"=>38,"room_id"=>10008,"name"=>"MakentDefault/vmtxlirxn9odqwmervnl","highlights"=>"","featured"=>"No"],
        ["id"=>39,"room_id"=>10008,"name"=>"MakentDefault/z2e6czrdqdlc9avenoyi","highlights"=>"","featured"=>"No"],
        ["id"=>40,"room_id"=>10008,"name"=>"MakentDefault/aozcbzhzlvntiy3ixim3","highlights"=>"","featured"=>"No"],
        ["id"=>41,"room_id"=>10008,"name"=>"MakentDefault/puqfkjbmqitovqiccdml","highlights"=>"","featured"=>"No"],
        ["id"=>42,"room_id"=>10008,"name"=>"MakentDefault/njslxtwul8l1iiqikcs6","highlights"=>"","featured"=>"Yes"],
        ["id"=>43,"room_id"=>10013,"name"=>"MakentDefault/zfrunkj3mmlx7knwhv49","highlights"=>"","featured"=>"No"],
        ["id"=>44,"room_id"=>10013,"name"=>"MakentDefault/ytpru1ijeluvg9osnssm","highlights"=>"","featured"=>"No"],
        ["id"=>45,"room_id"=>10013,"name"=>"MakentDefault/sgwkpmc3kbzwtnp7qgho","highlights"=>"","featured"=>"No"],
        ["id"=>46,"room_id"=>10013,"name"=>"MakentDefault/wl7zppeychvtmssomu0o","highlights"=>"","featured"=>"No"],
        ["id"=>47,"room_id"=>10013,"name"=>"MakentDefault/atz5mc52xvohzoa78wpv","highlights"=>"","featured"=>"Yes"],
        ["id"=>48,"room_id"=>10014,"name"=>"MakentDefault/h1kgzwuy2uk5ofnvfegx","highlights"=>"","featured"=>"No"],
        ["id"=>49,"room_id"=>10014,"name"=>"MakentDefault/ixwev9jmh4xawurimvbv","highlights"=>"","featured"=>"Yes"],
        ["id"=>50,"room_id"=>10014,"name"=>"MakentDefault/xndkxukqzzbegzeijzci","highlights"=>"","featured"=>"No"],
        ["id"=>51,"room_id"=>10014,"name"=>"MakentDefault/bcpkinwygwrw9hno4pxm","highlights"=>"","featured"=>"No"],
        ["id"=>52,"room_id"=>10014,"name"=>"MakentDefault/qok2ehbij09rkdfzmlbf","highlights"=>"","featured"=>"No"],
        ["id"=>53,"room_id"=>10014,"name"=>"MakentDefault/y0sdstehstlcmnmu0hna","highlights"=>"","featured"=>"No"],
        ["id"=>54,"room_id"=>10014,"name"=>"MakentDefault/nhcg0iqp0cq9awosajiy","highlights"=>"","featured"=>"No"],
        ["id"=>55,"room_id"=>10015,"name"=>"MakentDefault/x2mxsezgnaofqd3219hf","highlights"=>"","featured"=>"No"],
        ["id"=>56,"room_id"=>10015,"name"=>"MakentDefault/mvnsfjysgnhzkbp3l5ok","highlights"=>"","featured"=>"No"],
        ["id"=>57,"room_id"=>10015,"name"=>"MakentDefault/go3b2fyw9vwqg1bge0is","highlights"=>"","featured"=>"No"],
        ["id"=>58,"room_id"=>10015,"name"=>"MakentDefault/kvn8lujksexmmmus9ygx","highlights"=>"","featured"=>"No"],
        ["id"=>59,"room_id"=>10015,"name"=>"MakentDefault/sg6hhm4n1anjvtbpxtn9","highlights"=>"","featured"=>"No"],
        ["id"=>60,"room_id"=>10015,"name"=>"MakentDefault/icut5xr1svudynf86bby","highlights"=>"","featured"=>"No"],
        ["id"=>61,"room_id"=>10015,"name"=>"MakentDefault/nbzgw58xczswac0gcm2b","highlights"=>"","featured"=>"Yes"],
        ["id"=>62,"room_id"=>10016,"name"=>"MakentDefault/tzavbi03wo4jchpvu0gg","highlights"=>"","featured"=>"No"],
        ["id"=>63,"room_id"=>10016,"name"=>"MakentDefault/r2qljwrlk27pevokyumn","highlights"=>"","featured"=>"Yes"],
        ["id"=>64,"room_id"=>10016,"name"=>"MakentDefault/rfzsvkalfng4cxmtjwp6","highlights"=>"","featured"=>"No"],
        ["id"=>65,"room_id"=>10016,"name"=>"MakentDefault/d7chhp1digmw64ltdsrc","highlights"=>"","featured"=>"No"],
        ["id"=>66,"room_id"=>10016,"name"=>"MakentDefault/ljho3sn54xexisv1o941","highlights"=>"","featured"=>"No"],
        ["id"=>67,"room_id"=>10016,"name"=>"MakentDefault/uvhx6wotck8xj0s2omb2","highlights"=>"","featured"=>"No"],
        ["id"=>68,"room_id"=>10011,"name"=>"MakentDefault/ayawistwv6ocpqjxsbrn","highlights"=>"","featured"=>"No"],
        ["id"=>69,"room_id"=>10011,"name"=>"MakentDefault/fxbt00tkm9hu4de398bs","highlights"=>"","featured"=>"No"],
        ["id"=>70,"room_id"=>10011,"name"=>"MakentDefault/piix4l1wyjheysunucsu","highlights"=>"","featured"=>"No"],
        ["id"=>71,"room_id"=>10011,"name"=>"MakentDefault/cle5tszrp3hqkjlvrhhi","highlights"=>"","featured"=>"No"],
        ["id"=>72,"room_id"=>10011,"name"=>"MakentDefault/rjldsoqwh1bn4blx2kmg","highlights"=>"","featured"=>"No"],
        ["id"=>73,"room_id"=>10011,"name"=>"MakentDefault/rp0ebxesqckfwe9dnj1l","highlights"=>"","featured"=>"Yes"],
        ["id"=>85,"room_id"=>10005,"name"=>"MakentDefault/btxqgxm9xole2hbjukuw","highlights"=>"","featured"=>"No"],
        ["id"=>86,"room_id"=>10005,"name"=>"MakentDefault/ncbcwhfzdlif60ngmj1f","highlights"=>"","featured"=>"No"],
        ["id"=>87,"room_id"=>10005,"name"=>"MakentDefault/obz1gzamkea36bxo4y9r","highlights"=>"","featured"=>"No"],
        ["id"=>88,"room_id"=>10005,"name"=>"MakentDefault/c7gbnfmycsadvhvwk0qv","highlights"=>"","featured"=>"No"],
        ["id"=>89,"room_id"=>10005,"name"=>"MakentDefault/mstuzshwwjlurrvqysgo","highlights"=>"","featured"=>"No"],
        ["id"=>90,"room_id"=>10005,"name"=>"MakentDefault/ntkplzehj2mn0qghjzsm","highlights"=>"","featured"=>"No"],
        ["id"=>91,"room_id"=>10005,"name"=>"MakentDefault/bwx7p6w5yz1j0quu0srl","highlights"=>"","featured"=>"Yes"],
        ["id"=>92,"room_id"=>10003,"name"=>"MakentDefault/t8bypzqk2btzv0okdiru","highlights"=>"","featured"=>"Yes"],
        ["id"=>93,"room_id"=>10003,"name"=>"MakentDefault/pkulukziobwuaecffkew","highlights"=>"","featured"=>"No"],
        ["id"=>94,"room_id"=>10003,"name"=>"MakentDefault/yqob3txnghbgvjxto3cg","highlights"=>"","featured"=>"No"],
        ["id"=>95,"room_id"=>10003,"name"=>"MakentDefault/tiq2s1eltw2otvbupc8f","highlights"=>"","featured"=>"No"],
        ["id"=>96,"room_id"=>10003,"name"=>"MakentDefault/brpqwoxlr5h109xolxyt","highlights"=>"","featured"=>"No"],
        ["id"=>97,"room_id"=>10003,"name"=>"MakentDefault/c49kbnqhmgblwdurugxf","highlights"=>"","featured"=>"No"],
        ["id"=>98,"room_id"=>10004,"name"=>"MakentDefault/bfmyuqgyfnu8uokqqbcx","highlights"=>"","featured"=>"No"],
        ["id"=>99,"room_id"=>10004,"name"=>"MakentDefault/gsv1ctz70gnqifymovw4","highlights"=>"","featured"=>"No"],
        ["id"=>100,"room_id"=>10004,"name"=>"MakentDefault/tjxn6kkfubuhdmopmh1u","highlights"=>"","featured"=>"No"],
        ["id"=>101,"room_id"=>10004,"name"=>"MakentDefault/pejp6xy5snkidqyd2auu","highlights"=>"","featured"=>"No"],
        ["id"=>102,"room_id"=>10004,"name"=>"MakentDefault/sa02tj168g9spwcb3lwt","highlights"=>"","featured"=>"No"],
        ["id"=>103,"room_id"=>10004,"name"=>"MakentDefault/be1umc9pju5xxnavgfec","highlights"=>"","featured"=>"Yes"],
        ["id"=>104,"room_id"=>10018,"name"=>"MakentDefault/yicinfh4q2e1jauuavwg","highlights"=>"","featured"=>"No"],
        ["id"=>105,"room_id"=>10018,"name"=>"MakentDefault/t54zghk18etflo0oe2rf","highlights"=>"","featured"=>"Yes"]
      ]);
    }
}