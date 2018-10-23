%token T_TYPE                   type\b
%token T_NUM                    (\d+)
%token T_PLUS                   \+
%token T_STAR                   \*
%token T_NAME                   ([_A-Za-z][_0-9A-Za-z]+)

%skip T_WHITESPACE              (\xfe\xff|\x20|\x09|\x0a|\x0d)+
%skip T_COMMENT                 #[^\n]*

#Rule:
    Reduce()

Reduce:
    (<T_NUM> | <T_PLUS> #First) (<T_NUM> | Reduce() #Last)
