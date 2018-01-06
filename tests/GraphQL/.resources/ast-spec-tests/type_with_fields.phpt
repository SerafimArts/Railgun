--TEST--

Type with all fields definition

--FILE--

type A {
    id: ID
    idList: [ID]
    idNonNull: ID!
    idNonNullList: [ID]!
    idListOfNonNulls: [ID!]!

    int: Int
    intList: [Int]
    intNonNull: Int!
    intNonNullList: [Int]!
    intListOfNonNulls: [Int!]!

    float: Float
    floatList: [Float]
    floatNonNull: Float!
    floatNonNullList: [Float]!
    floatListOfNonNulls: [Float!]!

    string: String
    stringList: [String]
    stringNonNull: String!
    stringNonNullList: [String]!
    stringListOfNonNulls: [String!]!

    boolean: Boolean
    booleanList: [Boolean]
    booleanNonNull: Boolean!
    booleanNonNullList: [Boolean]!
    booleanListOfNonNulls: [Boolean!]!

    relation: Relation
    relationList: [Relation]
    relationNonNull: Relation!
    relationNonNullList: [Relation]!
    relationListOfNonNulls: [Relation!]!
}

--EXPECTF--

#Document
    #ObjectDefinition
        #Name
            token(T_NAME, A)
        #Field
            #Name
                token(T_NAME, id)
            #Type
                token(T_NAME, ID)
        #Field
            #Name
                token(T_NAME, idList)
            #List
                #Type
                    token(T_NAME, ID)
        #Field
            #Name
                token(T_NAME, idNonNull)
            #Type
                token(T_NAME, ID)
                token(T_NON_NULL, !)
        #Field
            #Name
                token(T_NAME, idNonNullList)
            #List
                #Type
                    token(T_NAME, ID)
                token(T_NON_NULL, !)
        #Field
            #Name
                token(T_NAME, idListOfNonNulls)
            #List
                #Type
                    token(T_NAME, ID)
                    token(T_NON_NULL, !)
                token(T_NON_NULL, !)
        #Field
            #Name
                token(T_NAME, int)
            #Type
                token(T_NAME, Int)
        #Field
            #Name
                token(T_NAME, intList)
            #List
                #Type
                    token(T_NAME, Int)
        #Field
            #Name
                token(T_NAME, intNonNull)
            #Type
                token(T_NAME, Int)
                token(T_NON_NULL, !)
        #Field
            #Name
                token(T_NAME, intNonNullList)
            #List
                #Type
                    token(T_NAME, Int)
                token(T_NON_NULL, !)
        #Field
            #Name
                token(T_NAME, intListOfNonNulls)
            #List
                #Type
                    token(T_NAME, Int)
                    token(T_NON_NULL, !)
                token(T_NON_NULL, !)
        #Field
            #Name
                token(T_NAME, float)
            #Type
                token(T_NAME, Float)
        #Field
            #Name
                token(T_NAME, floatList)
            #List
                #Type
                    token(T_NAME, Float)
        #Field
            #Name
                token(T_NAME, floatNonNull)
            #Type
                token(T_NAME, Float)
                token(T_NON_NULL, !)
        #Field
            #Name
                token(T_NAME, floatNonNullList)
            #List
                #Type
                    token(T_NAME, Float)
                token(T_NON_NULL, !)
        #Field
            #Name
                token(T_NAME, floatListOfNonNulls)
            #List
                #Type
                    token(T_NAME, Float)
                    token(T_NON_NULL, !)
                token(T_NON_NULL, !)
        #Field
            #Name
                token(T_NAME, string)
            #Type
                token(T_NAME, String)
        #Field
            #Name
                token(T_NAME, stringList)
            #List
                #Type
                    token(T_NAME, String)
        #Field
            #Name
                token(T_NAME, stringNonNull)
            #Type
                token(T_NAME, String)
                token(T_NON_NULL, !)
        #Field
            #Name
                token(T_NAME, stringNonNullList)
            #List
                #Type
                    token(T_NAME, String)
                token(T_NON_NULL, !)
        #Field
            #Name
                token(T_NAME, stringListOfNonNulls)
            #List
                #Type
                    token(T_NAME, String)
                    token(T_NON_NULL, !)
                token(T_NON_NULL, !)
        #Field
            #Name
                token(T_NAME, boolean)
            #Type
                token(T_NAME, Boolean)
        #Field
            #Name
                token(T_NAME, booleanList)
            #List
                #Type
                    token(T_NAME, Boolean)
        #Field
            #Name
                token(T_NAME, booleanNonNull)
            #Type
                token(T_NAME, Boolean)
                token(T_NON_NULL, !)
        #Field
            #Name
                token(T_NAME, booleanNonNullList)
            #List
                #Type
                    token(T_NAME, Boolean)
                token(T_NON_NULL, !)
        #Field
            #Name
                token(T_NAME, booleanListOfNonNulls)
            #List
                #Type
                    token(T_NAME, Boolean)
                    token(T_NON_NULL, !)
                token(T_NON_NULL, !)
        #Field
            #Name
                token(T_NAME, relation)
            #Type
                token(T_NAME, Relation)
        #Field
            #Name
                token(T_NAME, relationList)
            #List
                #Type
                    token(T_NAME, Relation)
        #Field
            #Name
                token(T_NAME, relationNonNull)
            #Type
                token(T_NAME, Relation)
                token(T_NON_NULL, !)
        #Field
            #Name
                token(T_NAME, relationNonNullList)
            #List
                #Type
                    token(T_NAME, Relation)
                token(T_NON_NULL, !)
        #Field
            #Name
                token(T_NAME, relationListOfNonNulls)
            #List
                #Type
                    token(T_NAME, Relation)
                    token(T_NON_NULL, !)
                token(T_NON_NULL, !)
