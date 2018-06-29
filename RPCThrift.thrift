namespace php DWDRPC.RPCThrift

struct FileData
{
    1:required string   name,                   // 文件名字
    2:required binary   buff,                   // 文件数据
}

struct Params {
    1: optional map<string,string> RPC_GET,
    2: optional map<string,string> RPC_POST,
    3: optional map<string,string> RPC_HEADER,
    4: optional FileData RPC_FILES,
}

service RPCService{
    string call(1:string controller 2:string action 3:Params params)
}